FROM composer:1.9.2

COPY . /app
WORKDIR /app
EXPOSE 8000

RUN docker-php-ext-install pdo_mysql
# 海外请注释下一行
RUN  rm composer.lock && composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
RUN composer update && chmod 777 ./entrypoint.sh
ENTRYPOINT [ "./entrypoint.sh" ]