package DataImport;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.util.Random;

public class Main {

    public static void importData() {
        System.out.println("-------- Mysql JDBC Connection Testing ------");

        try {
            Class.forName("com.mysql.cj.jdbc.Driver");
        } catch (ClassNotFoundException e) {
            System.out.println("Where is your Oracle JDBC Driver?");
            e.printStackTrace();
            return;
        }

        System.out.println("Oracle JDBC Driver Registered!");

        Connection connection = null;
        try {
            connection = DriverManager.getConnection("jdbc:mysql://185.5.52.148:3306/imse_db", "imse_user", "sSRTqm8NFBgw");
        } catch (SQLException e) {
            System.out.println("Connection Failed! Check output console");
            e.printStackTrace();
            return;
        }

        try {
            if (connection != null) {
                System.out.println("You made it, take control your database now!");
                Hotel.generate(connection);
                Zimmer.generate(connection);
                Reise.generate(connection);

                PersonGenerator personGenerator = PersonGenerator.getInstance(connection);
                personGenerator.generate();
            } else {
                System.out.println("Failed to make connection!");
            }

        } catch (Exception e) {
            e.printStackTrace();
            System.out.println("FAILED SQL EXEPTION");
        }

    }

    public static int random(int min, int max) {
        Random rand = new Random();

        return rand.nextInt((max - min) + 1) + min;
    }
}