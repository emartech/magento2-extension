FROM cypress/browsers:node12.6.0-chrome77

COPY package.json /test/

WORKDIR /test

RUN npm i && \
    ./node_modules/.bin/cypress install && \
    ./node_modules/.bin/cypress verify