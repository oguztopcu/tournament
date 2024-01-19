## Round Robin Tournament Simulation

## Requirements
```text
Docker version 24.0.7, build afdd53b
Docker Compose version v2.23.3-desktop.2
```

## Installations
First one
```text
git clone git@github.com:oguztopcu/tournament.git
```

move to project directory
```text
cd /path/to/your/tournament
docker-compose up -d
```

You should be change postgres database in environment file

```text
# Docker Postgres
POSTGRES_USER=
POSTGRES_PASSWORD=
POSTGRES_DB=
# End Docker Postgres
```

and you should run on your terminal at project directory 

```text
composer install
php artisan key:generate
php artisan migrate
```

If you haven't installed php, you connect to docker container you can run command

````text
docker ps (Running container lists)
docker exec -it {containerId} bash
````

Finally!

Go to your browser and open the link
```text
http://localhost
```
