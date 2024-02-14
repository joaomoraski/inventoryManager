FROM openjdk:17

WORKDIR /reports-java

COPY .mvn/ .mvn
COPY mvnw pom.xml ./

RUN chmod +x mvnw
RUN ./mvnw dependency:go-offline
COPY src ./src

RUN ./mvnw clean package

CMD ["./mvnw", "spring-boot:run"]
