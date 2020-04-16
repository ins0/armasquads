#!/bin/bash

echo "INIT BITOOLS REGEDIT"
su application -c 'wine regedit /entrypoint.d/regedit.reg'