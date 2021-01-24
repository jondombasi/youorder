package fr.mgmobile.gpstrack;

import android.content.Intent;
import android.graphics.Bitmap;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Environment;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.CompoundButton;
import android.widget.Switch;
import android.widget.TextView;
import android.widget.Toast;

import com.github.gcacace.signaturepad.views.SignaturePad;

import java.io.File;
import java.io.FileOutputStream;
import java.util.Date;
import java.util.List;

import rx.Observable;
import rx.Observer;
import rx.android.schedulers.AndroidSchedulers;
import rx.schedulers.Schedulers;

public class MainActivity extends AppCompatActivity {

    private static final String TAG = "MainActivity";
    private TextView mTextView;
    private SignaturePad mSignaturePad;
    private Button mButtonSave;
    private Button mButtonCancel;
    private String orderId = "756388649734";
    private Switch mSwitch;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        mTextView = (TextView) findViewById(R.id.textView);
        mSignaturePad = (SignaturePad) findViewById(R.id.signature);
        mButtonSave = (Button) findViewById(R.id.save);
        mButtonCancel = (Button) findViewById(R.id.cancel);
        mSwitch = (Switch) findViewById(R.id.switchTrack);
        mButtonSave.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Bitmap bm = mSignaturePad.getSignatureBitmap();
                new SaveBitmapTask().execute(bm);
            }
        });
        mButtonCancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                mSignaturePad.clear();
            }
        });
        mSwitch.setChecked(false);
        mSwitch.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
                Intent i = new Intent(MainActivity.this, MyService.class);
                if (isChecked) {
                    Log.i(TAG, "Tracking activated");
                    startService(i);
                } else {
                    stopService(i);
                    Log.i(TAG, "Tracking deactivated");
                }
            }
        });
    }

    private class SaveBitmapTask extends AsyncTask<Bitmap, Integer, File> {

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
        }

        @Override
        protected void onProgressUpdate(Integer... values) {
            super.onProgressUpdate(values);
        }

        @Override
        protected File doInBackground(Bitmap... params) {

            String root = Environment.getExternalStorageDirectory().toString();
            File myDir = new File(root + "/YouOrder");
            myDir.mkdirs();
            Date d = new Date();
            String fname = orderId + "_" + d.getTime() + ".jpg";
            File file = new File(myDir, fname);
            //Log.i("MainActivity", "" + fname);

            try {
                FileOutputStream out = new FileOutputStream(file);
                params[0].compress(Bitmap.CompressFormat.JPEG, 90, out);
                out.flush();
                out.close();
            } catch (Exception e) {
                e.printStackTrace();
            }
            return file;
        }

        @Override
        protected void onPostExecute(final File file) {
            super.onPostExecute(file);
            if (file.length() != 0) {
                Observable<List<GitHubRepo>> repos = RetrofitHelper.getGithubService().listRepos("midsylen");
                repos.subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(new Observer<List<GitHubRepo>>() {
                            @Override
                            public void onCompleted() {
                                Log.d(TAG, "Completed");
                                Log.d("File", file.getAbsolutePath());
                            }

                            @Override
                            public void onError(Throwable e) {
                                Log.d(TAG, "Error");
                                Log.d("File", file.getAbsolutePath());
                            }

                            @Override
                            public void onNext(List<GitHubRepo> gitHubRepos) {
                                Log.d(TAG, "Next");
                                Log.d(TAG, gitHubRepos.toString());
                            }
                        });
            } else {
                Toast.makeText(getApplicationContext(), "Error saving the file", Toast.LENGTH_LONG).show();
            }
        }
    }

}
