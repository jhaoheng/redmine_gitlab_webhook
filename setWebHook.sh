#!/bin/bash

# 產生文件
# 1. [repo name]/config.php
# 2. [repo name]/test.php
# 3. [repo name]/log/
# 4. [repo name]/webhook.php

echo ""
echo ""
echo "----------------------"
echo "This bash will create some file"
echo "* webhook"
echo "* | - [repo name]"
echo "*     | - config.php"
echo "*     | - log/"
echo "*     | - webhook.php"
echo "*     | - webhook_test.php"
echo ""
echo "if you want to leave, press ctrl+c..."

echo "please anser the follow step : "
read -p "token key : " tokenKey
read -p "redmine path : " redminePath
read -p "repository link : " repoLink


echo "----------------------"

echo ""

# 取得目前路徑
shRoot=$(pwd)

if [ ! -d "webhook" ] ; then
  mkdir webhook
fi
cd webhook
WebhookDIR=$(pwd)

# repoLink 字串處理
IFS='/' read -a array <<< "$repoLink"
#echo "repo字串長度: "${#array[@]}
repoName="${array[ ${#array[@]}-1 ]}"
echo "your repo name is: "$repoName

IFS='.' read -a array <<< "$repoName"
WH_folder="${array[ 0 ]}"
echo "your webhook folder name is: "$WH_folder

# 建立目錄/檔案
# 判斷folder資料夾是否存在
if [ ! -d "$WH_folder" ] ; then
    echo ""
else
    printf "\E[0;31;40m"
    echo "Error : folder $WH_folder exist , please check"
    printf "\E[0m"
    echo ""
    echo ""
    exit
fi

# 1. [webhook folder]
mkdir $WH_folder
cd $WH_folder

# 2. [webhook folder]/config.php
echo "<?php" > config.php
echo "//[setting info]" >> config.php
echo "\$tokenKey = "\'$tokenKey\'";" >> config.php
echo "\$redminePath = "\'$redminePath\'";" >> config.php
echo "\$repoLink = " \'$repoLink\'";" >> config.php
echo "?>" >> config.php

# 3. [repo name]/webhook_test.php
# 4  [repo name]/webhook.php
cd $shRoot/.sample
if [ -f "webhook_test.php" ] && [ -f  "webhook.php" ]; then
  cp webhook_test.php $WebhookDIR'/'$WH_folder
  cp webhook.php $WebhookDIR'/'$WH_folder
else
  printf "\E[0;31;40m"
  echo "Error : this sh lost .sample folder, please folk a new one."
  printf "\E[0m"
  rm -rf $WebhookDIR'/'$WH_folder
  exit
fi
cd $WebhookDIR'/'$WH_folder


# 5. [repo name]/log/
mkdir log && cd log && echo "" > latestLog.log && chmod 777 latestLog.log && cd ..

# 建立mirror
cd $redminePath

# 檢查在redine下是否已存在repo
if [ -d "$repoName" ] ; then
  printf "\E[0;31;40m"
  echo "Error : '$redminePath/$repoName' has been created, please check."
  printf "\E[0m"
  rm -rf $WebhookDIR'/'$WH_folder

  echo ""
  exit
fi

git clone --mirror $repoLink

# 檢查是否clone成功
if [ ! -d "$repoName" ] ; then
  printf "\E[0;31;40m"
  echo "Error : '$redminePath/$repoName' not mirror success.."
  echo "please check your 'redmine path' and 'git link'"
  printf "\E[0m"
  rm -rf $WebhookDIR'/'$WH_folder
  echo ""
  exit
fi

echo ""
