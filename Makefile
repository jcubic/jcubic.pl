all: build tidy

tidy:
	@./clean

build:
	@jekyll build

install:
	bundle install

now:
	@date +"%F %T%:z"
