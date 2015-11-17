FROM ubuntu:latest

RUN 	apt-get update; \
	apt-get install wget xz-utils default-jre ldap-utils php5-cli php5-ldap phpunit curl -y; \
	cd /tmp; wget http://archive.apache.org/dist/directory/apacheds/dist/2.0.0-M19/apacheds-2.0.0-M19-amd64.deb; \
	dpkg -i apacheds-2.0.0-M19-amd64.deb

RUN curl -sS https://getcomposer.org/installer | php ; mv composer.phar /usr/local/bin/composer

COPY . /root/

RUN	cd /var/lib/apacheds-2.0.0-M19/; rm -rf default/; tar -xvf /root/default.tar.xz

CMD ["/root/run.sh"]
