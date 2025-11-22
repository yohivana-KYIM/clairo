#!/usr/bin/env bash
set -euo pipefail

# =========================
# Config & helpers
# =========================
SONAR_HOME="${SONARQUBE_HOME:-/opt/sonarqube}"
SONAR_URL="${SONARQUBE_URL:-http://localhost:9000}"

ADMIN_OLD="${SONARQUBE_ADMIN_OLD:-admin}"
ADMIN_NEW="${SONARQUBE_ADMIN_NEW:-admin1234}"
TOKEN_NAME="${SONARQUBE_TECH_TOKEN_NAME:-local-scanner-token}"

SCANNER_SOURCES="${SCANNER_SOURCES:-/workspace}"
SCANNER_EXTRA="${SONAR_SCANNER_EXTRA_OPTS:-}"

BOOT_PROJECT_KEY="${SONARQUBE_BOOTSTRAP_PROJECT_KEY:-}"
BOOT_PROJECT_NAME="${SONARQUBE_BOOTSTRAP_PROJECT_NAME:-}"
BOOT_ORG_KEY="${SONARQUBE_BOOTSTRAP_ORG_KEY:-}"

LOG()  { printf '[bootstrap] %s\n' "$*" >&2; }
die()  { LOG "ERROR: $*"; LOG "Dumping recent logs:"; tail -n 200 "${SONAR_HOME}/logs/"*.log 2>/dev/null || true; exit 1; }

# curl wrapper WITHOUT -f so we can read 401/403 content when needed
http_code() {  # usage: http_code <url> [curl-args...]
  curl -sS -o /tmp/_resp.json -w "%{http_code}" "$@" || echo "000"
}

# =========================
# Start SonarQube (background)
# =========================
LOG "Starting SonarQube..."
if [[ -x "${SONAR_HOME}/bin/linux-x86-64/sonar.sh" ]]; then
  "${SONAR_HOME}/bin/linux-x86-64/sonar.sh" start
else
  die "sonar.sh not found at ${SONAR_HOME}/bin/linux-x86-64/sonar.sh"
fi

# =========================
# Wait for port to open
# =========================
LOG "Waiting for SonarQube web port 9000 to open..."
ATTEMPTS=240
until (exec 3<>/dev/tcp/127.0.0.1/9000) >/dev/null 2>&1; do
  ((ATTEMPTS--)) || die "Port 9000 never opened"
  sleep 2
done
exec 3>&- || true

# =========================
# Wait for status=UP (tolerate 401/403)
# =========================
STATUS_URL="${SONAR_URL}/api/system/status"
LOG "Polling ${STATUS_URL} until status=UP (tolerate 401/403 as web UP)..."
ATTEMPTS=240
while true; do
  CODE="$(http_code "${STATUS_URL}")"
  if [[ "${CODE}" == "200" ]] && grep -q '"status":"UP"' /tmp/_resp.json 2>/dev/null; then
    LOG "SonarQube is UP."
    break
  fi
  # Secured endpoints via reverse/front can return 401/403 though web is up
  if [[ "${CODE}" == "401" || "${CODE}" == "403" ]]; then
    LOG "Web responds ${CODE} (secured). Considering web UP."
    break
  fi

  ((ATTEMPTS--)) || {
    LOG "Timeout waiting for UP (last code=${CODE})"
    LOG "Last /api/system/status body:"; cat /tmp/_resp.json 2>/dev/null || true
    die "SonarQube didn't report UP in time"
  }
  sleep 2
done

# =========================
# Change admin password (idempotent)
# =========================
LOG "Ensuring admin password is set..."
set +e
curl -sS -u "admin:${ADMIN_OLD}" -X POST \
  "${SONAR_URL}/api/users/change_password" \
  -d "login=admin&previousPassword=${ADMIN_OLD}&password=${ADMIN_NEW}" >/dev/null
CHANGE_STATUS=$?
set -e
if [[ ${CHANGE_STATUS} -eq 0 ]]; then
  LOG "Admin password changed."
else
  LOG "Change password skipped (likely already changed)."
fi

# =========================
# Create/generate technical token
# =========================
LOG "Generating technical token '${TOKEN_NAME}'..."
GEN_JSON="$(curl -sS -u "admin:${ADMIN_NEW}" -X POST \
  "${SONAR_URL}/api/user_tokens/generate" \
  -d "name=${TOKEN_NAME}")" || die "Failed to call /api/user_tokens/generate"

# Extract token value (only returned once)
if command -v jq >/dev/null 2>&1; then
  TOKEN="$(echo "${GEN_JSON}" | jq -r '.token // empty')"
else
  TOKEN="$(echo "${GEN_JSON}" | sed -n 's/.*"token"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p')"
fi
[[ -n "${TOKEN}" ]] || die "No token value returned. Check permissions/edition."

LOG "Token generated."

# =========================
# (Optional) Create organization (EE/DC only)
# =========================
if [[ -n "${BOOT_ORG_KEY}" ]]; then
  LOG "Ensuring organization '${BOOT_ORG_KEY}' exists..."
  set +e
  curl -sS -u "admin:${ADMIN_NEW}" -X POST \
    "${SONAR_URL}/api/organizations/create" \
    -d "key=${BOOT_ORG_KEY}" -d "name=${BOOT_ORG_KEY}" >/dev/null
  set -e
fi

# =========================
# (Optional) Create project
# =========================
if [[ -n "${BOOT_PROJECT_KEY}" && -n "${BOOT_PROJECT_NAME}" ]]; then
  LOG "Ensuring project '${BOOT_PROJECT_KEY}' exists..."
  set +e
  curl -sS -H "Authorization: Bearer ${TOKEN}" -X POST \
    "${SONAR_URL}/api/projects/create" \
    -d "project=${BOOT_PROJECT_KEY}" \
    -d "name=${BOOT_PROJECT_NAME}" \
    $( [[ -n "${BOOT_ORG_KEY}" ]] && echo -n "-d organization=${BOOT_ORG_KEY}" ) >/dev/null
  set -e
fi

# =========================
# (Optional) Run a local scan
# =========================
if [[ -d "${SCANNER_SOURCES}" ]]; then
  LOG "Running sonar-scanner on ${SCANNER_SOURCES} (optional)…"
  pushd "${SCANNER_SOURCES}" >/dev/null
  PK="${BOOT_PROJECT_KEY:-local.project}"
  PN="${BOOT_PROJECT_NAME:-Local Project}"
  set +e
  sonar-scanner \
    -Dsonar.projectKey="${PK}" \
    -Dsonar.projectName="${PN}" \
    -Dsonar.sources=. \
    -Dsonar.host.url="${SONAR_URL}" \
    -Dsonar.login="${TOKEN}" \
    ${SCANNER_EXTRA}
  SCAN_RC=$?
  set -e
  if [[ ${SCAN_RC} -ne 0 ]]; then
    LOG "sonar-scanner exited with code ${SCAN_RC} (continuing)."
  fi
  popd >/dev/null
else
  LOG "SCANNER_SOURCES '${SCANNER_SOURCES}' not found. Skipping analysis."
fi

# =========================
# Follow logs in foreground
# =========================
LOG "Bootstrapping done. Following logs..."
exec tail -F \
  "${SONAR_HOME}/logs/sonar.log" \
  "${SONAR_HOME}/logs/web.log" \
  "${SONAR_HOME}/logs/ce.log"
