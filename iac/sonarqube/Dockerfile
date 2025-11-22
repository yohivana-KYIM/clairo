# iac/sonarqube/Dockerfile
FROM ubuntu:22.04

LABEL maintainer="Cleo DevOps" \
      description="All-in-one SonarQube + Scanner + Bootstrap image (APT-like install + ZIP fallback)"

# ====== (1) Versions & Paths ======
ARG SONARQUBE_VERSION=10.5.1.90531
ARG SONAR_SCANNER_VERSION=5.0.1.3006
ARG SONARQUBE_HOME=/opt/sonarqube
ARG WORKSPACE=/workspace

ENV SONARQUBE_HOME=${SONARQUBE_HOME}
ENV PATH="/opt/sonar-scanner/bin:${PATH}"

# ====== (2) System dependencies ======
RUN set -eux; \
    apt-get update; \
    DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
      openjdk-17-jre-headless curl wget unzip ca-certificates gnupg gosu \
      bash sed grep coreutils procps fontconfig postgresql-client jq; \
    rm -rf /var/lib/apt/lists/*

# ====== (3) Install SonarQube (try APT package if any, else official ZIP) ======
RUN set -eux; \
    if apt-cache search sonarqube | grep -q '^sonarqube'; then \
      echo 'Installing SonarQube from APT (community repo)...'; \
      apt-get update; apt-get install -y sonarqube; \
      ln -s /usr/lib/sonarqube ${SONARQUBE_HOME} || true; \
    else \
      echo 'No APT package found → installing from official ZIP'; \
      mkdir -p ${SONARQUBE_HOME}; \
      wget -q "https://binaries.sonarsource.com/Distribution/sonarqube/sonarqube-${SONARQUBE_VERSION}.zip" -O /tmp/sonarqube.zip; \
      unzip -q /tmp/sonarqube.zip -d /opt; \
      mv /opt/sonarqube-*/* ${SONARQUBE_HOME}/; \
      rm -rf /tmp/sonarqube.zip; \
    fi; \
    # Dedicated runtime user
    useradd -r -u 999 -g root sonarqube || true; \
    chown -R sonarqube:root ${SONARQUBE_HOME}; \
    chmod -R g+rwX ${SONARQUBE_HOME}

# ====== (4) Install Sonar Scanner CLI ======
RUN set -eux; \
    wget -q -O /tmp/sonar-scanner.zip \
      "https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-${SONAR_SCANNER_VERSION}-linux.zip"; \
    unzip -q /tmp/sonar-scanner.zip -d /opt; \
    mv /opt/sonar-scanner-* /opt/sonar-scanner; \
    rm -f /tmp/sonar-scanner.zip; \
    chown -R sonarqube:root /opt/sonar-scanner

# ====== (5) JVM options (tunable by env) ======
ENV SONAR_WEB_JAVAOPTS="-Xms512m -Xmx512m -XX:+UseG1GC -XX:MaxGCPauseMillis=200" \
    SONAR_CE_JAVAOPTS="-Xms512m -Xmx512m -XX:+UseG1GC" \
    SONAR_SEARCH_JAVAOPTS="-Xms1g -Xmx1g -Dnode.store.allow_mmap=false"

# ====== (6) Copy configuration (optional) & workspace ======
# Si tu as un sonar.properties personnalisé, décommente la ligne suivante
# COPY --chown=sonarqube:root iac/sonarqube/sonar.properties ${SONARQUBE_HOME}/conf/sonar.properties

# Copie du code à analyser (facultatif, sinon tu peux monter un volume)
COPY --chown=sonarqube:root . ${WORKSPACE}
WORKDIR ${WORKSPACE}
ENV SCANNER_SOURCES=${WORKSPACE}

# ====== (7) Bootstrap & Orchestration script ======
COPY --chown=sonarqube:root iac/sonarqube/start-and-bootstrap.sh /opt/start-and-bootstrap.sh
RUN sed -i 's/\r$//' /opt/start-and-bootstrap.sh && chmod +x /opt/start-and-bootstrap.sh

# ====== (8) Default envs (override in compose/env) ======
ENV SONARQUBE_URL="http://localhost:9000" \
    SONARQUBE_ADMIN_OLD="admin" \
    SONARQUBE_ADMIN_NEW="admin1234" \
    SONARQUBE_TECH_TOKEN_NAME="local-scanner-token" \
    SONARQUBE_BOOTSTRAP_PROJECT_KEY="" \
    SONARQUBE_BOOTSTRAP_PROJECT_NAME="" \
    SONARQUBE_BOOTSTRAP_ORG_KEY="" \
    SONAR_SCANNER_EXTRA_OPTS=""

# ====== (9) Healthcheck ======
HEALTHCHECK --interval=15s --timeout=5s --retries=40 \
  CMD curl -fsS "${SONARQUBE_URL}/api/system/status" | grep -q '"status":"UP"' || exit 1

EXPOSE 9000
USER sonarqube

ENTRYPOINT ["/opt/start-and-bootstrap.sh"]
