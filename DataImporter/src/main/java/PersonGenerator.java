import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.Statement;
import java.sql.Date.*;
import java.time.LocalDate;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

public class PersonGenerator {
    private static PersonGenerator instance = null;
    private final Connection connection;
    private Statement statement;
    private final List<String> firstNames;
    private final List<String> lastNames;
    private final List<String> months;
    private final List<String> emails;

    private PersonGenerator(Connection connection) {
        this.connection = connection;
        try {
            this.statement = connection.createStatement();
        } catch (Exception e) {
            e.printStackTrace();
        }

        this.firstNames = new ArrayList<>();
        this.lastNames = new ArrayList<>();
        this.months = new ArrayList<>();
        this.emails = new ArrayList<>();

        this.firstNames.addAll(Arrays.asList("Max", "Hubert"));
        this.lastNames.addAll(Arrays.asList("Musterman", "Meier"));
        this.months.addAll(Arrays.asList("JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"));
        this.emails.addAll(Arrays.asList("@gmail.com", "@yahoo.com", "@mail.ru", "@ymail.com", "@bb.aol.com", "@foo.bar", "@univie.ac.at", "@test.com", "@gmx.at", "@chinatown.de", "@meier.at"));
    }

    public static PersonGenerator getInstance(Connection connection) {
        if (null == instance) {
            instance = new PersonGenerator(connection);
        }

        return instance;
    }

    public void generate() {
        for (int i = 0; i < 100; ++i) {
            this.generate(i);
        }
    }

    private void generate(int id) {
        String date = String.valueOf(Main.random(1, 28))
                + '-'
                + this.months.get(Main.random(1, 12) - 1)
                + '-'
                + Main.random(17, 20);

        String firstName = this.firstNames.get(Main.random(0, this.firstNames.size() - 1));
        String lastName = this.lastNames.get(Main.random(0, this.lastNames.size() - 1));

        String name = String.format("%s %s", firstName, lastName);

        String insertSql = "INSERT INTO Person (Id, Name, Svnummer, Geburtsdatum, email) VALUES (?, ?, ?, ?, ?)";

        try {
            PreparedStatement preparedStatement = this.connection.prepareStatement(insertSql);
            preparedStatement.setInt(1, id);
            preparedStatement.setString(2, name);
            preparedStatement.setString(3, "" + Main.random(1, 2305));
            LocalDate today = LocalDate.now();

            preparedStatement.setDate(4, java.sql.Date.valueOf(today));
            preparedStatement.setString(5, firstName + this.emails.get(Main.random(0, this.emails.size() - 1)));

            preparedStatement.executeUpdate();
        } catch (Exception e) {
            System.err.println("Fehler beim Einfuegen des Datensatzes: " + e.getMessage());
        }
    }
}
