clean:
	rm -rf build

build:
	mkdir build
	ppm --no-intro --compile="src/JourneyCommando" --directory="build"

info:
	ppm --sdc="build/net.intellivoid.journey_commando.ppm"

generate_package:
	ppm --generate-package="src/JourneyCommando"

install:
	ppm --no-prompt --fix-conflict --cwarning --branch="production" --install="build/net.intellivoid.journey_commando.ppm"

run:
	ppm --main="net.intellivoid.journey_commando"