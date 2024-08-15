#!/usr/bin/env python3
import boto3
import json
import subprocess
import os
from dotenv import load_dotenv

# Load environment variables from .env file
load_dotenv()

def get_secret(secret_name, region_name="ca-central-1"):
    profile_name = os.getenv('AWS_PROFILE')
    if profile_name:
        session = boto3.Session(profile_name=profile_name)
    else:
        session = boto3.Session()
    client = session.client("secretsmanager", region_name=region_name)
    try:
        get_secret_value_response = client.get_secret_value(SecretId=secret_name)
        secret = get_secret_value_response["SecretString"]
        return secret
    except Exception as e:
        print(f"Error retrieving secret: {e}")
        return None

def write_to_env_file(secret, file_path=".env.production"):
    try:
        secret_dict = json.loads(secret)
        with open(file_path, "w") as f:
            for key, value in secret_dict.items():
                if isinstance(value, (dict, list)):
                    value = json.dumps(value)
                if key == "FIREBASE_CREDENTIALS":
                    f.write(f"{key}='{value}'\n")
                else:
                    f.write(f'{key}="{value}"\n')
        print(f"Secret written to {file_path}")
    except Exception as e:
        print(f"Error writing to file: {e}")

def delete_encrypted_file(file_path=".env.production.encrypted"):
    try:
        if os.path.exists(file_path):
            os.remove(file_path)
            print(f"Deleted {file_path}")
        else:
            print(f"{file_path} does not exist")
    except Exception as e:
        print(f"Error deleting file: {e}")

def encrypt_env_file(file_path=".env.production"):
    try:
        print(f"Encrypting {file_path}...")
        result = subprocess.run(["php", "artisan", "env:encrypt", "--env=production"], capture_output=True, text=True)
        if result.returncode != 0:
            print(f"Error in encryption command: {result.stderr}")
            return None
        output = result.stdout
        return output
    except Exception as e:
        print(f"Error encrypting file: {e}")
        return None

def set_vapor_key(output):
    try:
        key_line = next(line for line in output.split("\n") if "Key" in line)
        key = key_line.split()[-1]
        print(f"Encrypted Key: {key}")
        with open(".env.production", "w") as f:
            f.write(f'LARAVEL_ENV_ENCRYPTION_KEY={key}\n')
        expect_script = """
        spawn vapor env:push production --file=.env.production
        expect "Would you like to delete the environment file from your machine"
        send "yes\r"
        expect eof
        """
        result = subprocess.run(['expect', '-c', expect_script], capture_output=True, text=True)
        if result.returncode != 0:
            print(f"Error pushing .env.production to Vapor: {result.stderr}")
        else:
            print(result.stdout)
    except Exception as e:
        print(f"Error extracting key: {e}")

def main():
    secret_name = "learnlab/Serverless/Production"
    region_name = "ca-central-1"

    secret = get_secret(secret_name, region_name)
    if secret:
        write_to_env_file(secret)
        delete_encrypted_file()
        output = encrypt_env_file()
        if output:
            set_vapor_key(output)

if __name__ == "__main__":
    main()
