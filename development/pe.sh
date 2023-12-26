#!/bin/bash
# Prepare Environment
# This script creates additional folders outside of Framework for better security
# by Dmitry Semenov
# 8/7 - Init of Media folder
mkdir ../../media
chmod -R 775 ../../media
chown -R daemon.webmaster ../../media
