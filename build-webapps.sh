#!/bin/bash

for routerID in qc tr ottawa mtl; do
	sed -i'' "s/routerId      : .*,/routerId      : '$routerID',/" ./opentripplanner-webapp/src/main/webapp/js/otp/config.js
	cp ./opentripplanner-webapp/src/main/webapp/js/otp/config.js ./opentripplanner-webapp/target/opentripplanner-webapp/js/otp/config.js
	mvn package -DskipTests
	cp ./opentripplanner-webapp/target/opentripplanner-webapp.war ~/$routerID.war
done

