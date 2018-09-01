FROM 540688370389.dkr.ecr.eu-west-1.amazonaws.com/low-emedia/php:latest

COPY /appcode /applications/hal-cli-v2

WORKDIR /applications/hal-cli-v2

RUN apt-get update && apt-get install -y libfontconfig htop procps

RUN docker-php-ext-install pcntl

VOLUME /applications/hal-cli-v2

CMD ["/usr/local/bin/run.sh"]