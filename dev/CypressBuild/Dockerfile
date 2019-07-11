FROM cypress/browsers:chrome67

COPY package.json /test/

WORKDIR /test

RUN npm i && \
    ./node_modules/.bin/cypress install && \
    ./node_modules/.bin/cypress verify