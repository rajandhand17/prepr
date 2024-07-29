#!/bin/bash

# Check if an environment type is passed as an argument
if [ $# -eq 0 ]; then
    echo "Error: No environment type specified."
    echo "Usage: $0 <env_type>"
    exit 1
fi

# Environment type from the first argument
ENV_TYPE=$1

# Step 1: Delete the existing .env.${ENV_TYPE}.encrypted file
echo "Deleting existing .env.${ENV_TYPE}.encrypted file..."
rm -f .env.${ENV_TYPE}.encrypted

# Step 2: Generate a new .env.${ENV_TYPE}.encrypted using artisan command
echo "Generating new .env.${ENV_TYPE}.encrypted file..."
php artisan env:encrypt --env=$ENV_TYPE
