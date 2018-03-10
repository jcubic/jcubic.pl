all: build tidy

tidy:
	@./clean

build:
	@bundle exec jekyll build

install:
	bundle install

now:
	@date +"%F %T%:z"
