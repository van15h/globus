import MongoMigration.KundeMigration;
import MongoMigration.Migration;
import MongoMigration.MitarbeiterMigration;
import MongoMigration.ReiseBuroMigration;
import com.mongodb.MongoClient;
import com.mongodb.MongoCredential;
import com.mongodb.ServerAddress;
import com.mongodb.client.MongoDatabase;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.util.Arrays;

public class Main {
    public static void main(String[] args) {
        if (args[0].equals("import")) {
            DataImport.Main.main(args);
        }


        if (args[0].equals("migrate")) {
            migrateData();
        }

    }

    private static void migrateData() {
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

        MongoCredential credential = MongoCredential.createCredential("imse", "globus", "ms3".toCharArray());
        MongoClient mongoClient = new MongoClient(new ServerAddress("185.5.52.148", 27017), Arrays.asList(credential));
        MongoDatabase mongoDatabase = mongoClient.getDatabase("globus");

        Migration kundeMigration = new KundeMigration(connection, mongoClient, mongoDatabase);
        Migration mitarbeiterMigration = new MitarbeiterMigration(connection, mongoClient, mongoDatabase);
        Migration reiseBueroMigration = new ReiseBuroMigration(connection, mongoClient, mongoDatabase);
        reiseBueroMigration.migrate();
        mitarbeiterMigration.migrate();
    }
}
