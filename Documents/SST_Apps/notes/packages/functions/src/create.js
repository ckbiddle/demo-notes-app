import { Table } from "sst/node/table";
import * as uuid from "uuid";
import handler from "@notes/core/handler";
import dynamoDb from "@notes/core/dynamodb";

export const main = handler(async (event) => {
  // const data = JSON.parse(event.body);

  // JSON should look like this
  // { "requestContext":
  //   { "authorizer":
  //     { "iam":
  //       { "cognitoIdentity":
  //         { "identityId":"<IID>" }
  //       }
  //     }
  //   },
  //   "content":"Validated entry 1",
  //   "attachment":"image.jpg"
  // }

  console.log( "start event object ......" );
  console.log( JSON.stringify( event, null, '\t' ));
  console.log( "...... end event object" );

  // Chris Biddle, 08/22/2023
  // event.body came in as a string, with backslashes (\) preceding
  // the quotes. Pushing it through JSON.parse() converted it
  // back to a JSON object and got rid of the backslashes.
  const data = JSON.parse( event.body );

  console.log( "start data object ......" );
  console.log( JSON.stringify( data, null, '\t' ));
  console.log( "...... end data object" );

  const params = {
    TableName: Table.Notes.tableName,
    Item: {
      // The attributes of the item to be created
      userId: event.requestContext.authorizer.iam.cognitoIdentity.identityId, // The id of the author
      noteId: uuid.v1(), // A unique uuid
      content: data.content, // Parsed from request body
      attachment: data.attachment, // Parsed from request body
      createdAt: Date.now(), // Current Unix timestamp
    },
  };

  await dynamoDb.put(params);

  return params.Item;
});