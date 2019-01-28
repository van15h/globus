package com.company;

import com.mongodb.client.MongoCollection;
import com.mongodb.client.MongoCursor;
import com.mongodb.client.MongoDatabase;
import org.bson.Document;
import org.bson.types.ObjectId;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

import static com.mongodb.client.model.Filters.eq;
import static com.mongodb.client.model.Updates.set;

public class Reise {
    public static void handle(String[] args, MongoDatabase mdb) {
        switch (args[1]) {
            case "create": {
                Document doc = new Document()
                        .append("name", args[2])
                        .append("einreisedatum", args[3])
                        .append("reisedauer", args[4])
                        .append("preis", args[5]);
                List<ObjectId> hotelList = new ArrayList<>();
                hotelList.add(new ObjectId(args[6]));
                hotelList.add(new ObjectId(args[7]));
                doc.append("hotels", hotelList);
                MongoCollection<Document> collection = mdb.getCollection("Reise");
                collection.insertOne(doc);
                System.out.println("create successful");
                break;
            }
            case "read":
            {
                MongoCollection<Document> collection = mdb.getCollection("Reise");
                MongoCursor<Document> cursor = collection.find().iterator();
                try {
                    while (cursor.hasNext()) {
                        System.out.println(cursor.next().toJson());
                    }
                } finally {
                    cursor.close();
                }
                break;
            }

            case "update":{

                mdb.getCollection("Reise").updateOne(
                        eq("_id", new ObjectId(args[2])),
                        set("preis", args[3]));
                System.out.println("update successful");
                break;
            }
            case "delete":
            {
                MongoCollection<Document> collection = mdb.getCollection("Reise");
                collection.deleteOne(eq("_id", new ObjectId(args[2])));
                System.out.println("delete successfull");
                break;

            }
            default: throw new IllegalArgumentException("unknown command");
        }
    }
}
