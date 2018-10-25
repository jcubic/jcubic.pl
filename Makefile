all: build tidy index

index:
	@echo -e "Indexing..." && ./index.py _site > /dev/null && echo "            Done"

tidy:
	@./clean

build:
	@bundle exec jekyll build

watch:
	@bundle exec jekyll serve

install:
	@bundle install
	@pip install beautifulsoup4
	@pip install html5lib

now:
	@date +"%F %T%:z"
