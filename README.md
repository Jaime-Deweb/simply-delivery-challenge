# Simply Delivery Challenge API

## List Articles

- URL

	/api/items
	
- Method:

	GET

- URL Params
	
	Required:
	
	key=[string]
	
- Data Params
	
	None
	
- Success Response:

	Code: 200  
	Content: { id : 5, name : "Pizza", price : 1000 }
	
- Error Response:

	Code: 401 UNAUTHORIZED  
	Content: { error : "No API key provided."|"Invalid credentials." }

## Add Item
	
- URL

	/api/item
	
- Method:

	POST

- URL Params

	Required:
	
	key=[string]
	
- Data Params
	
	Required:

	name=[string] 
	price=[integer]
	
- Success Response:

	Code: 200  
	Content: { status : 200, success : "Item added successfully" }
	
- Error Response:

	Code: 401 UNAUTHORIZED  
	Content: { error : "No API key provided."|"Invalid credentials." }

	OR

	Code: 422  
	Content: { status => 422, errors => "Data no valid" }

## Update Item

- URL

	/api/item/:id
	
- Method:

	PUT

- URL Params

	Required:
	
	key=[string]  
	id=[integer}	
	
- Data Params
	
	Required:

	name=[string]  
	price=[integer]
	
- Success Response:

	Code: 200  
	Content: { status : 200, success : "Item updated successfully" }
	
- Error Response:

	Code: 401 UNAUTHORIZED  
	Content: { error : "No API key provided."|"Invalid credentials." }

	OR
	
	Code: 422  
	Content: { status => 422, errors => "Data no valid" }

## Delete Item

- URL

	/api/item/:id
	
- Method:

	DELETE

- URL Params

	Required:
	
	key=[string]  
	id=[integer}	
	
- Data Params
	
	None
	
- Success Response:

	Code: 200  
	Content: { status : 200, success : "Item deleted successfully" }
	
- Error Response:

	Code: 401 UNAUTHORIZED  
	Content: { error : "No API key provided."|"Invalid credentials." }

OR

	Code: 404  
	Content: { status => 422, errors => "Item not found" }

## Add Properties to Item

- URL

	/api/item/:id/properties
	
- Method:

	POST

- URL Params

	Required:
	
	key=[string]  
	id=[integer] (Item ID)	
	
- Data Params
	
	Required:

	properties=[array[string]]
	
- Success Response:

	Code: 200  
	Content: { status : 200, success : "The item properties were updated successfully" }
	
- Error Response:

	Code: 401 UNAUTHORIZED  
	Content: { error : "No API key provided."|"Invalid credentials." }

	OR

	Code: 404  
	Content: { status => 422, errors => "Item not found" }

	OR

	Code: 422  
	Content: { status : 422, errors : "Data no valid" }

- Notes:

	You should provide all item properties and not only the new ones.
