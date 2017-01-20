all: build tidy


tidy: build
	./clean

build:
	jekyll build
