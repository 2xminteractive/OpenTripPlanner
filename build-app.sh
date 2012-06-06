rm opentripplanner-api-webapp/src/main/resources/org/opentripplanner/api/application-context.xml opentripplanner-api-webapp/target/classes/org/opentripplanner/api/application-context.xml opentripplanner-api-webapp/target/opentripplanner-api-webapp/WEB-INF/classes/org/opentripplanner/api/application-context.xml

rm opentripplanner-api-webapp/target/classes/data-sources.xml opentripplanner-api-webapp/target/opentripplanner-api-webapp/WEB-INF/classes/data-sources.xml opentripplanner-api-webapp/src/main/resources/data-sources.xml

mvn -e integration-test -DskipTests
