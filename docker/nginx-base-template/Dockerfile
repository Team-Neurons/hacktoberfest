FROM nginx:1.19-alpine

# copy config template
COPY ./config/vhost.template /etc/nginx/conf.d/vhost.template

# clear workdir
RUN rm -rf /var/www/*

WORKDIR /var/www