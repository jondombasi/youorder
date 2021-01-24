package com.websmithing.gpstracker;

import android.app.AlarmManager;
import android.app.AlertDialog;
import android.app.PendingIntent;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.location.LocationManager;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.os.Handler;
import android.os.SystemClock;
import android.provider.Settings;
import android.support.v4.content.LocalBroadcastManager;
import android.support.v7.app.ActionBarActivity;
import android.util.Log;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.Window;
import android.view.inputmethod.EditorInfo;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.RadioGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.GooglePlayServicesUtil;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.UUID;

public class GpsTrackerActivity extends ActionBarActivity {
    private static final String TAG = "GpsTrackerActivity";

    // use the websmithing defaultUploadWebsite for testing and then check your
    // location with your browser here: https://www.websmithing.com/gpstracker/displaymap.php
    private String defaultUploadWebsite;

    private static TextView txtUserName;
    private static TextView content;
    private static EditText txtWebsite;
    private static Button trackingButton;

    public static boolean currentlyTracking;
    private RadioGroup intervalRadioGroup;
    private int intervalInMinutes = 1;
    public static int timechange = 30000;
    private AlarmManager alarmManager;
    private Intent gpsTrackerIntent;
    private PendingIntent pendingIntent;
    private String value = "";
    private String bonjour = "Bonjour ";
    private RelativeLayout relative;
    private ImageView imageView1;
    private Button refresh;
    boolean envoierreur = false;
   public static boolean buttonTouched = false;
    String id;
    String prenom;
    String nom;

    public String getid() {
        return id;
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        getWindow().requestFeature(Window.FEATURE_ACTION_BAR);

        setContentView(R.layout.activity_gpstracker);

        defaultUploadWebsite = getString(R.string.default_upload_website);

        txtWebsite = (EditText) findViewById(R.id.txtWebsite);
        relative = (RelativeLayout) findViewById(R.id.Relative);
        txtUserName = (TextView) findViewById(R.id.txtUserName);
        content = (TextView) findViewById(R.id.content);
        imageView1 = (ImageView) findViewById(R.id.imageView1);
        refresh = (Button) findViewById(R.id.refresh);
        //intervalRadioGroup = (RadioGroup) findViewById(R.id.intervalRadioGroup);
        trackingButton = (Button) findViewById(R.id.trackingButton);
        txtUserName.setImeOptions(EditorInfo.IME_ACTION_DONE);

        SharedPreferences sharedPreferencess = this.getSharedPreferences("com.websmithing.gpstracker.prefs", Context.MODE_PRIVATE);
        SharedPreferences.Editor editors = sharedPreferencess.edit();
        editors.putInt("timechange", 30000);
        editors.apply();


        //check en
        LocationManager lm = null;
        boolean gps_enabled = false, network_enabled = false;
        if (lm == null)
            lm = (LocationManager) this.getSystemService(Context.LOCATION_SERVICE);
        try {
            gps_enabled = lm.isProviderEnabled(LocationManager.GPS_PROVIDER);
        } catch (Exception ex) {
        }
        try {
            network_enabled = lm.isProviderEnabled(LocationManager.NETWORK_PROVIDER);
        } catch (Exception ex) {
        }

        if (!gps_enabled && !network_enabled) {
            AlertDialog.Builder dialog = new AlertDialog.Builder(this);
            dialog.setMessage(this.getResources().getString(R.string.gps_network_not_enabled));
            dialog.setPositiveButton(this.getResources().getString(R.string.open_location_settings), new DialogInterface.OnClickListener() {

                @Override
                public void onClick(DialogInterface paramDialogInterface, int paramInt) {
                    // TODO Auto-generated method stub
                    Intent myIntent = new Intent(Settings.ACTION_LOCATION_SOURCE_SETTINGS);
                    GpsTrackerActivity.this.startActivity(myIntent);
                    //get gps
                }
            });
            dialog.setNegativeButton(this.getString(R.string.Cancel), new DialogInterface.OnClickListener() {

                @Override
                public void onClick(DialogInterface paramDialogInterface, int paramInt) {
                    // TODO Auto-generated method stub

                }
            });
            dialog.show();

        }


        SharedPreferences sharedPreferences = this.getSharedPreferences("com.websmithing.gpstracker.prefs", Context.MODE_PRIVATE);
        currentlyTracking = sharedPreferences.getBoolean("currentlyTracking", false);
       /* intervalRadioGroup.setOnCheckedChangeListener( new RadioGroup.OnCheckedChangeListener() {
                    @Override
                    public void onCheckedChanged(RadioGroup radioGroup, int i) {
                        saveInterval();
                    }
                });*/

        boolean firstTimeLoadindApp = sharedPreferences.getBoolean("firstTimeLoadindApp", true);
        if (firstTimeLoadindApp) {
            SharedPreferences.Editor editor = sharedPreferences.edit();
            editor.putBoolean("firstTimeLoadindApp", false);
            editor.putString("appID", UUID.randomUUID().toString());
            editor.apply();
        }


        trackingButton.setOnClickListener(new View.OnClickListener() {
            public void onClick(View view) {
                trackLocation(view);
            }
        });

        Intent i = getIntent();
        if (i.hasExtra("keyName")) {
            value = i.getStringExtra("keyName");
        }
        JSONObject jObj = null;
        try {
            jObj = new JSONObject(value);
            id = jObj.getString("id");
            nom = jObj.getString("nom");
            prenom = jObj.getString("prenom");
            bonjour = "Bonjour " + prenom + " " + nom;

        } catch (JSONException e1) {
            e1.printStackTrace();
        }


    }

    private BroadcastReceiver reciever = new BroadcastReceiver() {
        @Override
        public void onReceive(Context context, Intent intent) {

            ConnectivityManager cm = (ConnectivityManager) context
                    .getSystemService(Context.CONNECTIVITY_SERVICE);
            System.out.println("display inten");

            NetworkInfo activeNetwork = cm.getActiveNetworkInfo();
            boolean isConnected = activeNetwork != null && activeNetwork.isConnectedOrConnecting();
            if (isConnected) {
                getSupportActionBar().show();
            } else
                getSupportActionBar().hide();

            relative.setVisibility(isConnected ? View.VISIBLE : View.GONE);
            imageView1.setVisibility(isConnected ? View.GONE : View.VISIBLE);
            refresh.setVisibility(isConnected ? View.GONE : View.VISIBLE);

        }
    };

    public void deconnecter() {
        SharedPreferences sharedPreferences = this.getSharedPreferences("com.websmithing.gpstracker.prefs", Context.MODE_PRIVATE);
        SharedPreferences.Editor editor = sharedPreferences.edit();
        GpsTrackerActivity.this.finish();
        cancelAlarmManager();
        currentlyTracking = false;
        editor.putBoolean("currentlyTracking", false);
        editor.putString("sessionID", "");
        editor.apply();
        setTrackingButtonState();
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle item selection
        switch (item.getItemId()) {
            case R.id.disconnect:
                deconnecter();
                return true;

            default:
                return super.onOptionsItemSelected(item);
        }
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.options_menu, menu);
        return true;
    }

    private void saveInterval() {
        if (currentlyTracking) {
            Toast.makeText(getApplicationContext(), R.string.user_needs_to_restart_tracking, Toast.LENGTH_LONG).show();
        }

        SharedPreferences sharedPreferences = this.getSharedPreferences("com.websmithing.gpstracker.prefs", Context.MODE_PRIVATE);
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putInt("intervalInMinutes", 1);
        /*
        switch (intervalRadioGroup.getCheckedRadioButtonId()) {
            case R.id.i1:
                editor.putInt("intervalInMinutes", 1);
                break;
            case R.id.i5:
                editor.putInt("intervalInMinutes", 5);
                break;
            case R.id.i15:
                editor.putInt("intervalInMinutes", 15);
                break;
        }*/

        editor.apply();
    }

    @Override
    public boolean onKeyDown(final int keyCode, final KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK) {
            moveTaskToBack(true);
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }

    private void startAlarmManager() {

        Log.d(TAG, "startAlarmManager");
        Context context = getBaseContext();
        alarmManager = (AlarmManager) context.getSystemService(Context.ALARM_SERVICE);
        gpsTrackerIntent = new Intent(context, GpsTrackerAlarmReceiver.class);
        pendingIntent = PendingIntent.getBroadcast(context, 0, gpsTrackerIntent, 0);
        SharedPreferences sharedPreferences = this.getSharedPreferences("com.websmithing.gpstracker.prefs", Context.MODE_PRIVATE);
        intervalInMinutes = sharedPreferences.getInt("intervalInMinutes", 1);
        alarmManager.setRepeating(AlarmManager.ELAPSED_REALTIME_WAKEUP,
                SystemClock.elapsedRealtime(),
                timechange, // 60000 = 1 minute
                pendingIntent);
    }

    protected void cancelAlarmManager() {
        Log.d(TAG, "cancelAlarmManager");

         sendDisconnectDataToWebsite();
        envoierreur = false;
        Context context = getBaseContext();
        Intent gpsTrackerIntent = new Intent(context, GpsTrackerAlarmReceiver.class);
        PendingIntent pendingIntent = PendingIntent.getBroadcast(context, 0, gpsTrackerIntent, 0);
        AlarmManager alarmManager = (AlarmManager) context.getSystemService(Context.ALARM_SERVICE);
        alarmManager.cancel(pendingIntent);
    }

    protected void sendDisconnectDataToWebsite() {
        SharedPreferences sharedPreferences = this.getSharedPreferences("com.websmithing.gpstracker.prefs", Context.MODE_PRIVATE);
        final RequestParams requestParams = new RequestParams();
        final String urlsite = "http://www.youorder.fr/admin/webservice/disconnectgps.php";
        requestParams.put("utilisateur", sharedPreferences.getString("userid", ""));

        LoopjHttpClient.get(urlsite, requestParams, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, org.apache.http.Header[] headers, byte[] responseBody) {
                LoopjHttpClient.debugLoopJ(TAG, "sendLocationDataToWebsite - success", urlsite, requestParams, responseBody, headers, statusCode, null);

            }

            @Override
            public void onFailure(int statusCode, org.apache.http.Header[] headers, byte[] errorResponse, Throwable e) {
                LoopjHttpClient.debugLoopJ(TAG, "sendLocationDataToWebsite - failure", urlsite, requestParams, errorResponse, headers, statusCode, e);

            }
        });
    }

    // called when trackingButton is tapped
    protected void trackLocation(View v) {
        SharedPreferences sharedPreferences = this.getSharedPreferences("com.websmithing.gpstracker.prefs", Context.MODE_PRIVATE);
        SharedPreferences.Editor editor = sharedPreferences.edit();
        LocationService.entier=0;
        if (!saveUserSettings()) {
            return;
        }

        if (!checkIfGooglePlayEnabled()) {
            return;
        }

        if (currentlyTracking) {
            buttonTouched=true;
            cancelAlarmManager();
            currentlyTracking = false;
            editor.putBoolean("currentlyTracking", false);
            editor.putString("sessionID", "");

        } else {

            startAlarmManager();
            currentlyTracking = true;
            editor.putBoolean("currentlyTracking", true);
            editor.putFloat("totalDistanceInMeters", 0f);
            editor.putBoolean("firstTimeGettingPosition", true);
            editor.putString("sessionID", UUID.randomUUID().toString());
        }

        editor.apply();
        setTrackingButtonState();
    }


    private boolean saveUserSettings() {


        SharedPreferences sharedPreferences = this.getSharedPreferences("com.websmithing.gpstracker.prefs", Context.MODE_PRIVATE);
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putInt("intervalInMinutes", 1);
//        switch (intervalRadioGroup.getCheckedRadioButtonId()) {
//            case R.id.i1:
//                editor.putInt("intervalInMinutes", 1);
//                break;
//            case R.id.i5:
//                editor.putInt("intervalInMinutes", 5);
//                break;
//            case R.id.i15:
//                editor.putInt("intervalInMinutes", 15);
//                break;
//        }

        editor.putString("userName", txtUserName.getText().toString().trim());
        editor.putString("defaultUploadWebsite", txtWebsite.getText().toString().trim());
        editor.putString("userid", id);
        editor.apply();

        return true;
    }


    private boolean hasSpaces(String str) {
        return ((str.split(" ").length > 1) ? true : false);
    }

    private void displayUserSettings() {
        SharedPreferences sharedPreferences = this.getSharedPreferences("com.websmithing.gpstracker.prefs", Context.MODE_PRIVATE);
        intervalInMinutes = sharedPreferences.getInt("intervalInMinutes", 1);

//        switch (intervalInMinutes) {
//            case 1:
//                intervalRadioGroup.check(R.id.i1);
//                break;
//            case 5:
//                intervalRadioGroup.check(R.id.i5);
//                break;
//            case 15:
//                intervalRadioGroup.check(R.id.i15);
//                break;
//        }

        txtWebsite.setText(sharedPreferences.getString("defaultUploadWebsite", defaultUploadWebsite));
        txtUserName.setText(bonjour);
    }

    private boolean checkIfGooglePlayEnabled() {
        if (GooglePlayServicesUtil.isGooglePlayServicesAvailable(this) == ConnectionResult.SUCCESS) {
            return true;
        } else {
            Log.e(TAG, "unable to connect to google play services.");
            Toast.makeText(getApplicationContext(), R.string.google_play_services_unavailable, Toast.LENGTH_LONG).show();
            return false;
        }
    }

    public static void setTrackingButtonState() {
        if (currentlyTracking) {
            content.setVisibility(View.VISIBLE);
            trackingButton.setBackgroundResource(R.drawable.red_tracking_button);
            trackingButton.setTextColor(Color.WHITE);
            trackingButton.setText(R.string.tracking_is_off);
        } else {
            content.setVisibility(View.GONE);
            trackingButton.setBackgroundResource(R.drawable.green_tracking_button);
            trackingButton.setTextColor(Color.WHITE);
            trackingButton.setText(R.string.tracking_is_on);
        }
    }
    private void desactiv() {
        Handler delayHandler = new Handler();
        Runnable r = new Runnable() {
            @Override
            public void run() {

                // Call this method after 1000 milliseconds
                SharedPreferences sharedPreferences = GpsTrackerActivity.this.getSharedPreferences("com.websmithing.gpstracker.prefs", Context.MODE_PRIVATE);
                SharedPreferences.Editor editor = sharedPreferences.edit();
                envoierreur = true;
                if (!saveUserSettings()) {
                    return;
                }

                if (!checkIfGooglePlayEnabled()) {
                    return;
                }




                cancelAlarmManager();
                currentlyTracking = false;
                editor.putBoolean("currentlyTracking", false);
                editor.putString("sessionID", "");


                editor.apply();
                setTrackingButtonState();

            }

        };
        delayHandler.postDelayed(r, 1000);

    }
    private void reactiv() {
        Handler delayHandler = new Handler();
        Runnable r = new Runnable() {
            @Override
            public void run() {

                // Call this method after 1000 milliseconds
                SharedPreferences sharedPreferences = GpsTrackerActivity.this.getSharedPreferences("com.websmithing.gpstracker.prefs", Context.MODE_PRIVATE);
                SharedPreferences.Editor editor = sharedPreferences.edit();
                envoierreur = true;
                if (!saveUserSettings()) {
                    return;
                }

                if (!checkIfGooglePlayEnabled()) {
                    return;
                }
                    startAlarmManager();
                    currentlyTracking = true;
                    editor.putBoolean("currentlyTracking", true);
                    editor.putFloat("totalDistanceInMeters", 0f);
                    editor.putBoolean("firstTimeGettingPosition", true);
                    editor.putString("sessionID", UUID.randomUUID().toString());


                editor.apply();
                setTrackingButtonState();

            }

        };
        delayHandler.postDelayed(r, 2000);
        if(LocationService.changebool)
            desactiv();

    }

    // handler for received Intents for the "my-event" event
    private BroadcastReceiver mMessageReceiver = new BroadcastReceiver() {
        @Override
        public void onReceive(Context context, Intent intent) {
            // Extract data included in the Intent
            String message = intent.getStringExtra("message");
            Log.d("receiver", "Got message: " + message);
            if (message.equals("datamess")) {

                SharedPreferences sharedPreferences = GpsTrackerActivity.this.getSharedPreferences("com.websmithing.gpstracker.prefs", Context.MODE_PRIVATE);
                SharedPreferences.Editor editor = sharedPreferences.edit();
                envoierreur = true;
                if (!saveUserSettings()) {
                    return;
                }

                if (!checkIfGooglePlayEnabled()) {
                    return;
                }

                if (currentlyTracking) {
                    cancelAlarmManager();
                    currentlyTracking = false;
                    editor.putBoolean("currentlyTracking", false);
                    editor.putString("sessionID", "");

                }
                editor.apply();
                reactiv();
            } else {

                envoierreur = true;
                /*if (currentlyTracking) {
                    cancelAlarmManager();
                    currentlyTracking = false;
                }
                setTrackingButtonState()*/;

            }
        }
    };

    @Override
    public void onResume() {
        Log.d(TAG, "onResumeT");
        super.onResume();
        App.activityResumed();
        displayUserSettings();
        setTrackingButtonState();
        // Register mMessageReceiver to receive messages.
        LocalBroadcastManager.getInstance(this).registerReceiver(mMessageReceiver,
                new IntentFilter("my-event"));
    }

    @Override
    protected void onStop() {
        super.onStop();
        unregisterReceiver(reciever);

    }

    @Override
    public void onPause() {
        super.onPause();
        LocalBroadcastManager.getInstance(this).unregisterReceiver(mMessageReceiver);
        App.activityPaused();


    }

    @Override
    public void onStart() {
        super.onStart();
        registerReceiver(reciever, new IntentFilter(
                "android.net.conn.CONNECTIVITY_CHANGE"));
    }
}
