all: build tidy

tidy:
	@./clean

build:
	@jekyll build

now:
	@date +"%F %T%:z"
