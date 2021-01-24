package fr.mgmobile.gpstrack;


import java.util.List;

import retrofit2.Retrofit;
import retrofit2.adapter.rxjava.RxJavaCallAdapterFactory;
import retrofit2.converter.gson.GsonConverterFactory;
import retrofit2.http.GET;
import retrofit2.http.Path;
import rx.Observable;

public class RetrofitHelper {

    private static final String API_URL = "https://api.github.com";

    private static GitHubService mGitHubService;

    public static GitHubService getGithubService() {
        if (mGitHubService == null) {
            Retrofit retrofit = new Retrofit.Builder()
                    .baseUrl(API_URL)
                    .addConverterFactory(GsonConverterFactory.create())
                    .addCallAdapterFactory(RxJavaCallAdapterFactory.create())
                    .build();
            mGitHubService = retrofit.create(GitHubService.class);
        }
        return mGitHubService;
    }

    interface GitHubService {
        @GET("/users/{user}/repos")
        Observable<List<GitHubRepo>> listRepos(@Path("user") String user);
    }
}