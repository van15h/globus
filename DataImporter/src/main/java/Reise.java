import java.sql.*;
import java.time.LocalDate;

public class Reise {
  static Statement stmt;
 
  public static void generate(Connection connection) {
    try {
		stmt = connection.createStatement();

		System.out.println("Reise connected!");
    } catch (Exception e) {
      System.err.println(e.getMessage());
    }


    //GENERATE HOTELS
	String[] names = {
		"Perfekt Moment",
		"Luxus",
		"Wellness und Spa",
		"Honeymoon",
		"Familien Trip",
		"Magic Life",
		"Best Time",
		"Traumschiff,",
		"Die Welt zusammen",
		"Stadttrip"
	};
	String[] months = {"JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"};


	for (int i = 1; i <= 1000; i++) {
		LocalDate today = LocalDate.now();
		today.withYear(Main.random(2018, 2021));
		today.withDayOfYear(Main.random(1, 365));
		
		String insertSql = "INSERT INTO Reise (Id, name, einreisedatum, reisedauer, preis) VALUES (?, ?, ?, ?, ?)";

		try {
			PreparedStatement preparedStatement = connection.prepareStatement(insertSql);
            preparedStatement.setInt(1, i);
			preparedStatement.setString(2, names[ Main.random(1, 10) - 1 ]);
			preparedStatement.setDate(3, java.sql.Date.valueOf(today));
			preparedStatement.setInt(4, Main.random(1, 30));
			preparedStatement.setInt(5, Main.random(10, 1000));

			preparedStatement.executeUpdate();
		} catch (Exception e) {
  			System.err.println("Fehler beim Einfuegen des Datensatzes: " + e.getMessage());
		}
	}
  }
}  