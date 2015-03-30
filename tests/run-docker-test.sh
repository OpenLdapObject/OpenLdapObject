#!/bin/bash

if [ -z $1 ];
then
    ACTION="--no-bash";
else
    ACTION="--bash";
fi

docker build -t olo-docker-test tests/docker-test/
if [ "$ACTION" == "--bash" ];
then
    docker run -t -i -v $(pwd)/:/root/src -v $(pwd)/tests/docker-test/log/:/root/log --env run_bash=true olo-docker-test
else
    docker run -t -i -v $(pwd)/:/root/src -v $(pwd)/tests/docker-test/log/:/root/log --env run_bash=false olo-docker-test
fi
RESULT=$(cat $(pwd)/tests/docker-test/log/test.result);
if [ "$RESULT" == "success" ];
then
    exit 0;
else
    exit -1;
fi
