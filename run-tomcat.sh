
JVM_MAX_MEMORY="2660m"

#DEBUG="-Xdebug -Xrunjdwp:transport=dt_socket,server=y,address=8765,suspend=y"
#DEBUG="-Xdebug -Xrunjdwp:transport=dt_socket,server=y,address=8765,suspend=n"
DEBUG=""

if [ "$OTP_HOME" = "" ]; then
	OTP_HOME=`pwd`
fi

JAVA_HOME="/usr/java/jdk1.7.0_04"
#JAVA_HOME="/usr/java/jdk1.6.0_26"

#JAVA_OPTS="$DEBUG -Xmx$JVM_MAX_MEMORY -Xms128m -XX:PermSize=48m -XX:MaxPermSize=128m"
#JAVA_OPTS="-server $DEBUG -Xmx$JVM_MAX_MEMORY -Xms1g -XX:PermSize=128m -XX:MaxPermSize=256m"
JAVA_OPTS="-server $DEBUG -Xmx$JVM_MAX_MEMORY -Xms2g -XX:PermSize=48m -XX:MaxPermSize=128m"

cd $OTP_HOME
cp logging.properties opentripplanner-integration/target/tomcat6x/container/conf/logging.properties
cp logging.properties opentripplanner-integration/target/cargo/installs/apache-tomcat-6.0.35/apache-tomcat-6.0.35/conf/logging.properties
cp opentripplanner-api-webapp/src/main/resources/data-sources.xml opentripplanner-integration/target/tomcat6x/container/webapps/opentripplanner-api-webapp/WEB-INF/classes/data-sources.xml
cp opentripplanner-api-webapp/src/main/resources/org/opentripplanner/api/application-context.xml opentripplanner-integration/target/tomcat6x/container/webapps/opentripplanner-api-webapp/WEB-INF/classes/org/opentripplanner/api/application-context.xml

nohup java $JAVA_OPTS -Dcatalina.home=$OTP_HOME/opentripplanner-integration/target/cargo/installs/apache-tomcat-6.0.35/apache-tomcat-6.0.35 -Dcatalina.base=$OTP_HOME/opentripplanner-integration/target/tomcat6x/container -Djava.io.tmpdir=$OTP_HOME/opentripplanner-integration/target/tomcat6x/container/temp -Djava.util.logging.manager=org.apache.juli.ClassLoaderLogManager -Djava.util.logging.config.file=$OTP_HOME/opentripplanner-integration/target/tomcat6x/container/conf/logging.properties -classpath $OTP_HOME/opentripplanner-integration/target/cargo/installs/apache-tomcat-6.0.35/apache-tomcat-6.0.35/bin/bootstrap.jar:$JAVA_HOME/lib/tools.jar org.apache.catalina.startup.Bootstrap start &

