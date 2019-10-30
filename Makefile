all: build tidy

.PHONY: index tidy build watch install sitemap

index:
	@echo "Indexing..." && ./index.py _site > /dev/null && echo "            Done"

tidy:
	@echo "cleaning..."
	@./clean

build: prod
	@bundle exec jekyll build

watch:
	@bundle exec jekyll serve

install:
	@bundle install
	@pip install beautifulsoup4
	@pip install html5lib

now:
	@date +"%F %T%:z"

prod:
  export JEKYLL_ENV=production

sitemap:
	@sed -i "s/lastmod: .*/lastmod: `./date`/" _config.yml
