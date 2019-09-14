FROM alpine

LABEL maintainer "Jakub T. Jankiewicz <jcubic@onet.pl>"

RUN apk add --update --no-cache git ruby ruby-dev python \
    cmake libxslt nodejs build-base py-pip python-dev bash \
    libffi libxml2 zlib

RUN git clone https://github.com/htacg/tidy-html5 /tmp/tidy-html5 && \
    cd /tmp/tidy-html5/build/cmake && \
    cmake ../.. -DCMAKE_BUILD_TYPE=Release && \
    make && make install

RUN rm -rf /tmp/tidy-html5

RUN echo "gem: --no-ri --no-rdoc" > ~/.gemrc

RUN yes | gem update --system

RUN gem install --force bundler

RUN ln -s node /usr/bin/nodejs

COPY Gemfile /tmp

WORKDIR /tmp

RUN bundle config --global silence_root_warning 1 && \
    bundle install && \
    pip install --user --no-warn-script-location \
    https://github.com/jcubic/pygments-lexer-babylon/zipball/master

RUN apk --no-cache del cmake build-base python-dev ruby-dev

RUN rm -rf /root/.gem

WORKDIR /tmp/www/
CMD jekyll serve --host 0.0.0.0 --config _config.yml,_config_docker.yml
