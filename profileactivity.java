package com.example.tabulate;
import java.util.ArrayList;
import java.util.LinkedHashMap;
import java.util.Queue;

import parsing.Parse;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.Spinner;
import android.widget.TextView;
import database.AsyncResponse;
import database.Database;

public class ProfileActivity extends FragmentActivity implements AsyncResponse
{
	  LinkedHashMap<String,String> map= new LinkedHashMap<String, String>();
	  TextView bottles;
	  TextView pints ;
	  TextView total;
	  private Parse parse;
	  private Spinner bottleSpinner;
	  private ArrayList<String> bottleList;
	  private ArrayAdapter<String> bottleAdapter;
	  
	  private Spinner pintSpinner;
	  private ArrayList<String> pintList;
	  private ArrayAdapter<String> pintAdapter;
	  private String customer;
	  
	  public void onCreate(Bundle savedInstanceState) 
	    {
		 
	        super.onCreate(savedInstanceState);
	        setContentView(R.layout.activity_profile_);
	        
	        //init buttons
	        Button  btnPaid = (Button)findViewById(R.id.paidBtn);
	        parse=new Parse();
	        //init views
	        bottles = (TextView)findViewById(R.id.tvNumberBottles);
	    	pints = (TextView)findViewById(R.id.tvNumberPints);
	    	total = (TextView)findViewById(R.id.tvTotal);
	        
	    	 
	    	 //spinners
	    	bottleSpinner = (Spinner) findViewById(R.id.bottleSpinner);
	    	pintSpinner= (Spinner) findViewById(R.id.pintSpinner);
	    	//lists
	    	bottleList = new ArrayList<String>();
	    	bottleList.add("Select Bottle");
	    	pintList = new ArrayList<String>();
	    	pintList.add("Select Pint");
	    	 //adapters
	    	bottleAdapter = new ArrayAdapter<String>(this, android.R.layout.simple_spinner_item,bottleList );
	    	bottleAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
	    	// Apply the adapter to the spinner
	    	bottleSpinner.setAdapter(bottleAdapter);
	    	bottleSpinner.setOnItemSelectedListener(new ItemSelectedListener());
	    	
	    	pintAdapter= new ArrayAdapter<String>(this, android.R.layout.simple_spinner_item,pintList );
	    	pintAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
	    	    // Apply the adapter to the spinner
	    	pintSpinner.setAdapter(pintAdapter);
	    	pintSpinner.setOnItemSelectedListener(new ItemSelectedListener());
	    	
	        //init listeners
	        btnPaid.setOnClickListener(new PaidListener());
	        
	        TextView name = (TextView)findViewById(R.id.profileNameBtn);////
	        customer=getIntent().getExtras().getString("name");
	        name.setText(customer);
	        //add name to param list for json
	        //map.put("name", getIntent().getExtras().getString("name"));
	        
	        addToMap("","","","","");
	        //get bottles from db
	        new Database (map,"beer/retrieveBottles",this).execute();
	       // new Database (map,"customers/get_beers.php",this).execute();
	    }
	  
	  public void addToMap(String name, String keg, String costp,String costb,String qb)
	  {
	  	    map.put("name", name);
	        map.put("quantity_keg", keg);
	        map.put("cost_pint", costp);
	        map.put("cost_bottle", costb);
	        map.put("quantity_bottle", qb);
	        map.put("event_id", getIntent().getExtras().getString("event_id"));
	  }	  
	    
	    class PaidListener implements OnClickListener
	    {

	    	  public void onClick(View v)
	    	    {
	    		  new Database(map,"customers/paid").execute();
	    		  //TextView tv = (TextView) findViewById(R.id.name_list);
	    		 // tv.setText("This is strike-thru");
	    		// tv.setPaintFlags(tv.getPaintFlags() | Paint.STRIKE_THRU_TEXT_FLAG);
	    	       //cross name off
	    		  //change status?
	    	    }
	    }
	    
	    public void displayTab()
	    {
	    	 //get # pints and bottles from database
	    }
	    
	    public void processFinish(String output)
		{
	    	
	    	System.out.println("output: "+output);
	    	if(output.contains("retrieveBottles"))
	    	{
	    		addToBottleList(output);
		        addToMap("","","","","");
		        new Database (map,"beer/retrievePints",this).execute();
	    	}

	    	else if(output.contains("retrievePints"))
	    		addtoPintsList(output);
	    	else if (output.contains("updateBottleQuantity"))
	    	{
	    		if(output.contains("updated successfully"))
	    		{
	    			//update the bottle spinner
	    			  bottleList.clear();
	    			  bottleList.add("Select Bottle");
	    			  addToMap("","","","","");
	    		      new Database (map,"beer/retrieveBottles",this).execute();
	    		      System.out.println("retrieving bottles");
	    		}
	    	}
	    		
	    		
		}
	    
	    public void decrementQuantity()
	    {
	    	//get the current quantity, and decrement by 1
	    	
	    	
	    }
	    
	    public class ItemSelectedListener implements OnItemSelectedListener 
	    {
	    	public void getName(String s,int pos)
	    	{
	    		 //get name of beer selected
	    		 int index = s.indexOf(" ");
	    		 String result = s.substring(0,index);
	    		 String tokens[]=result.split(":");
	    		 System.out.println(tokens[1]);
	    		 addToMap(tokens[1],"","","","");
	    	}
	    	 
	    	public void onItemSelected(AdapterView<?> parent, View view, int position,
	    			long id)
	    	{
	    		//determine if bottle or pint
	    		if(position>0)
	    			getName( parent.getItemAtPosition(position).toString(),position);
	    		//check if bottle or pint
	    		if( parent.getItemAtPosition(0).toString().equals("Select Bottle") && position>0)
	    		{
	    			  //decrement number of bottles in database
	    			 new Database (map,"beer/updateBottleQuantity",ProfileActivity.this).execute();
	    			 bottleSpinner.setSelection(0);
		    		  //update customer bottles and total
	    			 
	    		}
	    		else
	    		{
		    		//update customer pints and total
	    			//addToMap(customer,"","","","");
	    			//new Database (map,"sales/updatePints",ProfileActivity.this).execute();
	    			//new Database (map,"sales/updateTotal",ProfileActivity.this).execute();
	    		}
	    		  
	    	}

			public void onNothingSelected(AdapterView<?> arg0) {
				
			}
	    }
		
		public String incrementByOne(CharSequence c)
		{
			int x=Integer.parseInt(c.toString())+1;
			
			return x+"";
			
		}
		
		public void addToBottleList(String response)
		{
			if(response.contains("name")) //check for empty strings
			{
				//break up response into lines containing name, cost, and quantity
		    	parse.setString(response);
			    String tokens[]=parse.beers().split(",");
			    
			
				for(String s: tokens)
				{
					//check quanity of bottles, if quanity <0 dont add
					int spaceindex=parse.nthOccurrence(s,' ',2);
					int index = s.indexOf("quantity:");
					String result = s.substring(index,spaceindex);
					String tok[]=result.split(":");
					// add each bottle to the adapter list if quantity>0
					if(Double.parseDouble(tok[1].replace("cost_each", ""))>0)
						bottleList.add(s);
				}
			}
				
		}
		public void addtoPintsList(String response)
		{
			//parse the string of names returned from the database
	    	parse.setString(response);
	    	String tokens[]=parse.beers().split(",");
			for(String s: tokens)
			{
				//check quanity of bottles, if quanity <0 dont add
				int spaceindex=parse.nthOccurrence(s,' ',2);
				int index = s.indexOf("quantity:");
				String result = s.substring(index,spaceindex);
				String tok[]=result.split(":");
				// add each bottle to the adapter list if quantity>0
				if(Double.parseDouble(tok[1].replace("cost_each", ""))>0)
					pintList.add(s);
			}
				
		}
		
		public void addToHashMap(String s)
		{
			
			
		}
		

		
}
