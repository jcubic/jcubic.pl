SOURCES := $(shell git ls-files _posts _layouts _includes _plugins css img _config.yml)

all: build tidy

.PHONY: tidy watch install sitemap deploy

tidy:
	@echo "cleaning..."
	@./clean

.last_build: $(SOURCES)
	@JEKYLL_ENV=production bundle exec jekyll build
	@echo "Indexing..." && ./index.py _site > /dev/null && echo "            Done"
	@touch $@

build: .last_build

.last_deploy: .last_build
	./_deploy
	scp index.db mydevil:~/jcubic.pl/ < /dev/null
	@touch $@

deploy: .last_deploy

watch:
	@bundle exec jekyll serve

install:
	@bundle install
	@pip install beautifulsoup4
	@pip install html5lib

now:
	@date +"%F %T%:z"

sitemap:
	@sed -i "s/lastmod: .*/lastmod: `./date`/" _config.yml
