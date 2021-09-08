# -------------------------------------------------------
# Start up script for running Weeding Helper in Docker
# to Kick the tires and see what it can do
# -------------------------------------------------------

# Build the Application Images
docker build -t dzoladz/weeding-helper-app -f docker/app/Dockerfile .
docker build -t dzoladz/weeding-helper-db -f docker/db/Dockerfile .

# Bring the Application Up
docker compose -f docker/docker-compose.yml up -d