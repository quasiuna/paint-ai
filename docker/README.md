# docker / hosting info

Below is a basic set up following the creation of a new Ubuntu 22.04 (jammy) server in the cloud.

## SSH INTO THE CLOUD SERVER
ssh user@example

## INSTALL DOCKER
sudo apt update
sudo apt install make apt-transport-https ca-certificates curl software-properties-common
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu jammy stable"
apt-cache policy docker-ce
sudo apt install docker-ce

## CHECK DOCKER INSTALLED
sudo systemctl status docker

## CREATE SSH KEY
ssh-keygen
cat ~/.ssh/id_rsa.pub
- In Github create a read-only Deploy Key on the appropriate repo and paste in this public key

## Create a "web" user with ID 1000
useradd -u 1000 -d /home/web -m -s /bin/bash web

## Add app to server
cd /home/web
git clone git@github.com:quasiuna/paint-ai.git

## Start docker
cd /home/web/paint-ai/docker
docker compose up -d

## Test
- The app should now be up and running with SSL, via nginx in the swag container, on https://example.com
