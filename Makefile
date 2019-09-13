all: build tidy

.PHONY: index tidy build watch install

index:
	@echo "Indexing..." && ./index.py _site > /dev/null && echo "            Done"

tidy:
	@echo "cleaning..."
	@clean

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
