package fr.mgmobile.gpstrack;

import android.app.Service;
import android.content.Intent;
import android.location.Location;
import android.os.IBinder;
import android.util.Log;

import com.google.android.gms.location.LocationRequest;

import pl.charmas.android.reactivelocation.ReactiveLocationProvider;
import rx.Observer;
import rx.Subscription;

public class MyService extends Service {

    private Subscription mLocationSubscription;
private long mInterval = 1000*5;


    public MyService() {
    }

    @Override
    public int onStartCommand(Intent intent, int flags, int startId) {
        LocationRequest request = LocationRequest.create() //standard GMS LocationRequest
                .setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY)
                .setInterval(mInterval)
                .setFastestInterval(mInterval);

        ReactiveLocationProvider locationProvider = new ReactiveLocationProvider(this);
        mLocationSubscription = locationProvider.getUpdatedLocation(request)
                //        .filter(...)    // you can filter location updates
                //.map(...)       // you can map location to sth different
                //.flatMap(...)   // or event flat map
                //...             // and do everything else that is provided by RxJava
                .subscribe(new Observer<Location>() {
                    @Override
                    public void onCompleted() {

                    }

                    @Override
                    public void onError(Throwable e) {

                    }

                    @Override
                    public void onNext(Location location) {
                        Log.d("Location", location.toString());
                    }
                });/*new Action1<Location>() {
                    @Override
                    public void call(Location location) {
                        //mTextView.setText(mTextView.getText().toString() + "\n" + location.toString());
                        Log.d("Location", location.toString());
                    }
                })*/;
        return super.onStartCommand(intent, flags, startId);
    }

    @Override
    public void onDestroy() {
        if (mLocationSubscription != null) {
            mLocationSubscription.unsubscribe();
        }
        super.onDestroy();
    }

    @Override
    public IBinder onBind(Intent intent) {
        return null;
    }
}
