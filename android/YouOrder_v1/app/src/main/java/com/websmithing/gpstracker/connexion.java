package com.websmithing.gpstracker;

/**
 * Created by mac on 01/04/15.
 */

import android.app.Activity;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.SharedPreferences;
import android.graphics.PorterDuffColorFilter;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;

import org.json.JSONArray;

public class connexion extends Activity {
    EditText nom, num;
    LinearLayout viewconnect;
    TextView error;
    Button ok;
    private static final String TAG_USER = "";
    private static final String TAG_ID = "id";
    private static final String TAG_NAME = "nom";
    private static final String TAG_Prenom = "prenom";
    private JSONArray user = null;
    private String res = "";

    private Button refresh;
    ProgressBar progress;
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.connexion);
        nom = (EditText) findViewById(R.id.email);
        num = (EditText) findViewById(R.id.password);
        viewconnect = (LinearLayout) findViewById(R.id.LinearLayout);
        refresh = (Button) findViewById(R.id.refresh);
        progress = (ProgressBar) findViewById(R.id.progressBarre);
        progress.getIndeterminateDrawable().setColorFilter(
                new PorterDuffColorFilter(getResources()
                        .getColor(R.color.green),
                        android.graphics.PorterDuff.Mode.MULTIPLY));
        progress.setVisibility(View.GONE);
        ok = (Button) findViewById(R.id.button1);
        ok.setOnClickListener(new View.OnClickListener() {


            public void onClick(View v) {
                new Connection().execute();
                progress.setVisibility(View.VISIBLE);
                connexion.this.progress.setProgress(0);

            }

        });
        if (!isTaskRoot()) {
            finish();
            return;
        }
    }

    private class Connection extends AsyncTask {
        @Override
        protected Object doInBackground(Object... arg0) {
            connect();
            return null;
        }

    }

    public static boolean isNumeric(String str) {
        try {
            double d = Double.parseDouble(str);
        } catch (NumberFormatException nfe) {
            return false;
        }
        return true;
    }

    private BroadcastReceiver reciever = new BroadcastReceiver() {
        @Override
        public void onReceive(Context context, Intent intent) {

            ConnectivityManager cm = (ConnectivityManager) context
                    .getSystemService(Context.CONNECTIVITY_SERVICE);
            System.out.println("display inten");
            NetworkInfo activeNetwork = cm.getActiveNetworkInfo();
            boolean isConnected = activeNetwork != null && activeNetwork.isConnectedOrConnecting();
            viewconnect.setVisibility(isConnected ? View.VISIBLE : View.GONE);
            refresh.setVisibility(isConnected ? View.GONE : View.VISIBLE);


        }
    };

    private void connect() {

        Intent intent = new Intent(connexion.this, GpsTrackerActivity.class);
        intent.putExtra("keyName", "test");
        startActivity(intent);
/*
        ArrayList<NameValuePair> postParameters = new ArrayList<NameValuePair>(2);
        postParameters.add(new BasicNameValuePair("email", nom.getText().toString()));
        postParameters.add(new BasicNameValuePair("password", num.getText().toString()));

//String valid = "1";
        String response = null;
        try {
            // response = CustemHttpClient.executeHttpPost("http://www.youorder.fr/admin/inc_connexion_mobile.php", postParameters);
            response = CustemHttpClient.executeHttpPost("http://www.youorder.fr/admin/webservice/connexion_mobile.php", postParameters);
            res = response.toString();
            Log.i("", "ok " + res);
            //   res = res.trim();
            //   res= res.replaceAll("\\s+","");

            if (isNumeric(res)) {
                Log.i("", "email ou mot de passe incorrect");
                runOnUiThread(new Runnable() {
                    @Override
                    public void run() {
                        Toast.makeText(getApplicationContext(), "email ou mot de passe invalid",
                                Toast.LENGTH_LONG).show();
                    }
                });
            } else if (!res.equals("-1") && !res.equals("")) {
                runOnUiThread(new Runnable() {

                    @Override
                    public void run() {
//                        Toast.makeText(getApplicationContext(), "connexion réussi",
//                                Toast.LENGTH_LONG).show();
                        Intent intent = new Intent(connexion.this, GpsTrackerActivity.class);
                        intent.putExtra("keyName", res);
                        startActivity(intent);

                        if (!saveUserSettings()) {
                            return;
                        }
                    }
                });
            } else {
                runOnUiThread(new Runnable() {

                    @Override
                    public void run() {
                        Toast.makeText(getApplicationContext(), "erreur de connexion",
                                Toast.LENGTH_LONG).show();
                    }
                });
            }

            progress.setVisibility(View.GONE);
            viewconnect.invalidate();

        } catch (Exception e) {
            runOnUiThread(new Runnable() {

                @Override
                public void run() {

                    progress.setVisibility(View.GONE);
                    viewconnect.invalidate();



                }
            });

            Log.i("", "exception e" + e);
//un.setText(e.toString());

        }*/
    }

    private void displayUserSettings() {
        SharedPreferences sharedPreferences = this.getSharedPreferences("com.websmithing.gpstracker.prefs", Context.MODE_PRIVATE);
        nom.setText(sharedPreferences.getString("login", ""));
        num.setText(sharedPreferences.getString("mdp", ""));
    }

    private boolean saveUserSettings() {


        SharedPreferences sharedPreferences = this.getSharedPreferences("com.websmithing.gpstracker.prefs", Context.MODE_PRIVATE);
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putString("login", nom.getText().toString().trim());
        editor.putString("mdp", num.getText().toString().trim());
        editor.apply();
        return true;
    }

    @Override
    public void onDestroy() {
        super.onDestroy();
    }


    @Override
    public void onStart() {
        super.onStart();
        registerReceiver(reciever, new IntentFilter(
                "android.net.conn.CONNECTIVITY_CHANGE"));
    }

    @Override
    public void onStop() {
        super.onStop();
        unregisterReceiver(reciever);
    }

    @Override
    public void onResume() {
        Log.d("", "onResume");
        super.onResume();
        displayUserSettings();

    }

}
