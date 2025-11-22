#!/bin/bash

# Load environment variables from .env or .env.local
if [ -f .env.local ]; then
    export $(grep -v '^#' .env.local | xargs)
elif [ -f .env ]; then
    export $(grep -v '^#' .env | xargs)
fi
pwd
ls
# Parse the DATABASE_URL
if [[ $DATABASE_URL =~ mysql://([^:]+):([^@]+)@([^:]+)(:[0-9]+)?/(.+) ]]; then
    export MYSQL_USER="${BASH_REMATCH[1]}"
    export MYSQL_PASSWORD="${BASH_REMATCH[2]}"
    export MYSQL_HOST="${BASH_REMATCH[3]}"
    export MYSQL_PORT="${BASH_REMATCH[4]}"
    export MYSQL_DATABASE="${BASH_REMATCH[5]}"
    export MYSQL_ROOT_PASSWORD="root"  # Change this if needed
else
    echo "DATABASE_URL format is invalid."
    exit 1
fi

# Print the extracted variables (optional)
echo "Extracted MySQL Environment Variables:"
echo "MYSQL_USER=$MYSQL_USER"
echo "MYSQL_PASSWORD=$MYSQL_PASSWORD"
echo "MYSQL_DATABASE=$MYSQL_DATABASE"
echo "MYSQL_ROOT_PASSWORD=$MYSQL_ROOT_PASSWORD"
