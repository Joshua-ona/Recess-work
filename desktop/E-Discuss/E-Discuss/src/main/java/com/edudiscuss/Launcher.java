package com.edudiscuss;

/**
 * Entry point used when running the app directly from an IDE run
 * configuration or `java -cp ...` (e.g. IntelliJ's green Run button).
 *
 * JavaFX refuses to launch a class that directly extends
 * javafx.application.Application unless it's started on the
 * module-path (--module-path / --add-modules). Since this project
 * runs JavaFX from the plain classpath (via Maven), that check fails
 * with "JavaFX runtime components are missing" even though the
 * JavaFX jars are right there.
 *
 * Routing through a separate main class that does NOT extend
 * Application sidesteps that check entirely — the JVM sees an
 * ordinary class, and only loads JavaFX classes once inside
 * Main.main(), by which point plain classpath resolution works fine.
 *
 * `mvn javafx:run` (via the javafx-maven-plugin in pom.xml) isn't
 * affected by this and can keep pointing at Main directly.
 */
public class Launcher {

    public static void main(String[] args) {
        Main.main(args);
    }
}
