### Installation using Docker

It is possible to install Hubzilla comfortably and conveniently as a Docker container. Saiwal ([sk@hub.utsukta.org](https://hub.utsukta.org/channel/sk)) offers a preconfigured environment for a Hubzilla container for this purpose.

The key features are:

- Use Docker Compose to set up a fully functional [Hubzilla](https://hubzilla.org/page/info/discover) instance with just a few commands.
- Prebuilt images available via dockerhub for amd64, arm/v7, arm64.
- Continuous Updates: The Docker image is built to allow for easy  updates whenever new changes are made to the Hubzilla core or its  dependencies.
- SMTP Integration: Built-in support for sending emails using ssmtp,  making it easy to configure email notifications for your Hubzilla  instance.

The repository for the container is located here: [**skprg/hubzilla-docker**](https://github.com/skprg/hubzilla-docker)

#### Building the image from scratch

- Clone the Repository:

```
git clone https://github.com/skprg/hubzilla-docker.git
cd hubzilla-docker
```

- Configure Your Environment: Update the `docker-compose.yml` file with your SMTP and other settings.
- Build and Run the Container:

```
docker-compose up --build -d
```

#### Using prebuilt image

- Replace the following lines in `docker-compose.yml`

```
    build:
      context: .
      dockerfile: Dockerfile 
```

with

```
    image: ghcr.io/skprg/hubzilla-docker:latest
```

- Run the container:

```
docker compose up -d
```

Access Your Hubzilla Instance: Navigate to http://localhost (or the appropriate URL) to view your Hubzilla instance.

#### **Further notes / update / tips** 

Further notes, upgrade instructions and tips can be found in the repository linked above.