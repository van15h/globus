package DataImport;

import java.sql.*;

public class Zimmer {
  static Statement stmt;
 
  public static void generate(Connection connection) {
    try {
		stmt = connection.createStatement();

		System.out.println("Zimmers connected!");
    } catch (Exception e) {
      System.err.println(e.getMessage());
    }


    //GENERATE ZIMMERS
	String[] variations = {"EZ", "DZ", "Suit", "DeLUX"};


	for (int i = 1; i <= 100; i++) {
		String insertSql = "INSERT INTO Zimmer (ID, HOTELID, NUMMER, VARIATION) VALUES ("
			+ i
			+ ", " + DataImport.Main.random(1, 100)
			+ ", " + DataImport.Main.random(1, 1000)
			+ ", '" + variations[ DataImport.Main.random(1, 4) - 1 ] + "'"
		+ ")";

		try {
  			stmt.executeUpdate(insertSql);
		} catch (Exception e) {
  			System.err.println("Fehler beim Einfuegen des Datensatzes: " + e.getMessage());
		}
	}
  }
}  