FROM alpine:3.11

LABEL maintainer="Jakub T. Jankiewicz <jcubic@onet.pl>"

RUN echo "https://dl-cdn.alpinelinux.org/alpine/v3.11/main" > /etc/apk/repositories && \
    echo "https://dl-cdn.alpinelinux.org/alpine/v3.11/community" >> /etc/apk/repositories

RUN apk add --update --no-cache git ruby ruby-dev python2 libffi-dev \
    build-base cmake libxslt nodejs make build-base py-pip python2-dev \
    bash libffi libxml2 zlib zlib-dev sed yaml-dev \
    ruby-etc ruby-bigdecimal ruby-json python3 python3-dev

ENV LIBRARY_PATH=/lib:/usr/lib

RUN git clone https://github.com/htacg/tidy-html5 --depth 1 /tmp/tidy-html5 && \
    cd /tmp/tidy-html5/build/cmake && \
    cmake ../.. -DCMAKE_BUILD_TYPE=Release && \
    make && make install

RUN rm -rf /tmp/tidy-html5

RUN gem install rdoc -v '6.7.0' --no-document

RUN echo "gem: --no-ri --no-rdoc" > ~/.gemrc

#RUN yes | gem update --system 3.4.22 --no-document

RUN gem install --force bundler -v '2.3.26' --no-document

RUN gem pristine psych --version 5.5.0

RUN ln -s node /usr/bin/nodejs

COPY Gemfile /tmp

WORKDIR /tmp

RUN bundle config --global silence_root_warning 1 && \
    bundle config --global jobs 1 && \
    bundle install

RUN pip install --user --no-warn-script-location \
    https://github.com/jcubic/pygments-lexer-babylon/zipball/master

#RUN gem pristine --all

RUN pip install "beautifulsoup4==4.9.3" html5lib==1.1

#RUN apk --no-cache del cmake ruby-dev build-base

#RUN rm -rf /root/.gem

WORKDIR /tmp/www/

RUN git config --global --add safe.directory /tmp/www

CMD jekyll serve --host 0.0.0.0 --config _config.yml,_config_docker.yml
