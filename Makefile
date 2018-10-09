all: build tidy

tidy:
	@./clean

build:
	@bundle exec jekyll build

watch:
	@bundle exec jekyll serve

install:
	bundle install

now:
	@date +"%F %T%:z"
