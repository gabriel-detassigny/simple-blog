#! /bin/bash

if [[ -z "${SB_PROD_SERVER}" ]]; then
    exit "SB_PROD_SERVER env variable needs to be set\n"
fi

if [[ -z "${SB_PROD_DIR}" ]]; then
    exit "SB_PROD_DIR env variable needs to be set\n"
fi

rm -rf /tmp/deploy

git clone git@github.com:gabriel-detassigny/simple-blog.git /tmp/deploy

cd /tmp/deploy && composer install -o --no-dev --ignore-platform-reqs
rsync -avz --include="frontend/public/images/upload/.gitkeep" --exclude="frontend/public/images/upload/*" \
  --exclude=".git" --exclude=".gitignore" --exclude=".travis.yml" --exclude=".env" \
  --exclude="tests" --exclude="phpunit.xml" --exclude="logs/app-errors.log" --exclude="README.md" \
  --exclude="LICENSE.md" \
  . $SB_PROD_SERVER:$SB_PROD_DIR --delete

ssh $SB_PROD_SERVER "source ~/.bash_profile && cd $SB_PROD_DIR && \
    vendor/bin/phinx migrate -e production && vendor/bin/doctrine orm:generate-proxies"
