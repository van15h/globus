package DataImport;

import java.sql.*;

public class Hotel {
  static Statement stmt;
 
  public static void generate(Connection connection) {
    try {
		stmt = connection.createStatement();

		System.out.println("Hotels connected!");
    } catch (Exception e) {
      System.err.println(e.getMessage());
    }


    //GENERATE HOTELS
	String[] names = {"Europe", "Double Tree", "Capital", "Center", "Hilton", "Mariott", "Park", "Residence", "Loft", "Green"};
	String[] verpflegungs = {"BB", "HP", "All"};
	String[] orts = {"Minsk", "Kyiv", "Wien", "Moskau", "Paris", "London", "Amsterdam", "Barcelona", "Rome", "Rotterdam"};
	String[] strasses = {"Sonnenschein 2", "Sonnenschein 3", "Strand 3", "BIgman 62", "BIgman 11", "Sonnenschein 12", "BIgman 1", "Strand 91", "BIgmanein 2", "BIgman 99"};


	for (int i = 0; i <= 100; i++) {
		String insertSql = "INSERT INTO Hotel (id, name, sterne, verpflegung, plz, ort, strasse) VALUES ("
			+ i
			+ ", '" + names[ DataImport.Main.random(1, 10) - 1 ] + "'"
			+ ", " + DataImport.Main.random(1, 5)
			+ ", '" + verpflegungs[ DataImport.Main.random(1, 3) - 1] + "'"
			+ ", " + DataImport.Main.random(1000, 9999)
			+ ", '" + orts[ DataImport.Main.random(1, 10) - 1 ] + "'"
			+ ", '" + strasses[ DataImport.Main.random(1, 10) - 1 ] + "'"
		+ ")";

		try {
  			stmt.executeUpdate(insertSql);
		} catch (Exception e) {
  			System.err.println("Fehler beim Einfuegen des Datensatzes: " + e.getMessage());
		}
	}
  }
}  