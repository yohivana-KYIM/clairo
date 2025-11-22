# Dockerfile for MySQL service
FROM mariadb

COPY ./iac/scripts/set_db_env.sh /iac/scripts/set_db_env.sh
COPY .env.local /.env.local
COPY .env /.env
RUN chmod +x /iac/scripts/set_db_env.sh \
    && /iac/scripts/set_db_env.sh
