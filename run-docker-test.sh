#!/bin/bash

docker build -t olo-docker-test tests/docker-test/
docker run -t -i -v $(pwd)/:/root/src -v $(pwd)/tests/docker-test/log/:/root/log --env run_bash=false olo-docker-test
RESULT=$(cat $(pwd)/tests/docker-test/log/test.result);
if [ "$RESULT" == "success" ];
then
    exit 0;
else
    exit -1;
fi
