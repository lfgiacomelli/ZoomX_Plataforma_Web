#!/bin/bash

envsubst < /etc/msmtprc.template > /etc/msmtprc
chmod 600 /etc/msmtprc

apache2-foreground
