import pandas as pd
import numpy as np
import datetime

# required machine learning packages
from sklearn import model_selection
from sklearn.neural_network import MLPClassifier
from sklearn import metrics
from sklearn.model_selection import learning_curve
from sklearn.model_selection import ShuffleSplit
from sklearn.model_selection import train_test_split

def main():
    '''Machine Learning Model to predict moneyline outcome to NFL games'''
    df = pd.read_csv('spreadspoke_scores.csv')
    teams = pd.read_csv('nfl_teams.csv')

    df = df.replace(r'^\s*$', np.nan, regex=True)

    df['over_under_line'] = df.over_under_line.astype(float)
    df['team_home'] = df.team_home.map(teams.set_index('team_name')['team_id'].to_dict())
    df['team_away'] = df.team_away.map(teams.set_index('team_name')['team_id'].to_dict())

    df.loc[df.team_favorite_id == df.team_home, 'home_favorite'] = 1
    df.loc[df.team_favorite_id == df.team_away, 'away_favorite'] = 1
    df.home_favorite.fillna(0, inplace=True)
    df.away_favorite.fillna(0, inplace=True)

    df.loc[((df.score_home + df.score_away) > df.over_under_line), 'over'] = 1
    df.over.fillna(0, inplace=True)

    df.loc[(df.schedule_week == '18'), 'schedule_week'] = '17'
    df.loc[(df.schedule_week == 'Wildcard') | (df.schedule_week == 'WildCard'), 'schedule_week'] = '18'
    df.loc[(df.schedule_week == 'Division'), 'schedule_week'] = '19'
    df.loc[(df.schedule_week == 'Conference'), 'schedule_week'] = '20'
    df.loc[(df.schedule_week == 'Superbowl') | (df.schedule_week == 'SuperBowl'), 'schedule_week'] = '21'
    df['schedule_week'] = df.schedule_week.astype(int)

    df = df.drop(df[df.schedule_season < 2000].index)
    df = df.drop(df[df.schedule_season > 2020].index)
    df['schedule_date'] = pd.to_datetime(df['schedule_date'])

    df.reset_index(drop=True, inplace=True)

    for team in teams.team_id.unique().tolist():
        for season in range(1999,2021): 
            
            wins, games_played = 0., 0.
            
            for week in range(1,18):
                current_game = df[((df.team_home == team) | (df.team_away == team)) & (df.schedule_season == season) & (df.schedule_week == week)]
                
                # If a game exists
                if(current_game.shape[0] == 1):
                    current_game = current_game.iloc[0]
                    
        
                    if ((current_game.team_home == team) & (current_game.score_home > current_game.score_away)):
                        wins += 1
                        
                    elif ((current_game.team_away == team) & (current_game.score_away > current_game.score_home)):
                        wins += 1
                    
                    # If not a tie count game as part of record
                    if(current_game.score_away != current_game.score_home):
                        games_played += 1
                    
                    # If week one put default record as 0
                    if(week == 1):
                        if(current_game.team_home == team):
                            df.loc[(df.team_home == team) & (df.schedule_season == season) & (df.schedule_week == week), 'team_home_current_win_pct'] = 0 
                        else:
                            df.loc[(df.team_away == team) & (df.schedule_season == season) & (df.schedule_week == week), 'team_away_current_win_pct'] = 0 

                # Put record for next week game and account for bye week
                next_week_game = df[((df.team_home == team) | (df.team_away == team)) & (df.schedule_season == season) & (df.schedule_week == week+1)]
                # If a game exists
                if(next_week_game.shape[0] == 1):
                    next_week_game = next_week_game.iloc[0]
                    if(next_week_game.team_home == team):
                        df.loc[(df.team_home == team) & (df.schedule_season == season) & (df.schedule_week == week+1), 'team_home_current_win_pct'] = 0 if games_played == 0 else wins/games_played
                    else:
                        df.loc[(df.team_away == team) & (df.schedule_season == season) & (df.schedule_week == week+1), 'team_away_current_win_pct'] = 0 if games_played == 0 else wins/games_played
                else: # Bye week
                    next_twoweek_game = df[((df.team_home == team) | (df.team_away == team)) & (df.schedule_season == season) & (df.schedule_week == week+2)]
                    # If a game exists
                    if(next_twoweek_game.shape[0] == 1):
                        next_twoweek_game = next_twoweek_game.iloc[0]
                        if(next_twoweek_game.team_home == team):
                            df.loc[(df.team_home == team) & (df.schedule_season == season) & (df.schedule_week == week+2), 'team_home_current_win_pct'] = 0 if games_played == 0 else wins/games_played
                        else:
                            df.loc[(df.team_away == team) & (df.schedule_season == season) & (df.schedule_week == week+2), 'team_away_current_win_pct'] = 0 if games_played == 0 else wins/games_played

                        
            # if beyond week 17 (playoffs use season record)
            for postseason_week in range(18,22):
                current_game = df[((df.team_home == team) | (df.team_away == team)) & (df.schedule_season == season) & (df.schedule_week == postseason_week)]
                # If a game exists
                if(current_game.shape[0] == 1):
                    current_game = current_game.iloc[0]
                    if(current_game.team_home == team):
                        df.loc[(df.team_home == team) & (df.schedule_season == season) & (df.schedule_week == postseason_week), 'team_home_current_win_pct'] = 0 if games_played == 0 else wins/games_played
                    else:
                        df.loc[(df.team_away == team) & (df.schedule_season == season) & (df.schedule_week == postseason_week), 'team_away_current_win_pct'] = 0 if games_played == 0 else wins/games_played
            
            # if week 17 put current in next season
            # if last season is 2000 (no record) put in as 
            next_season = season+1
            for week in range(1,22):
                next_season_game = df[((df.team_home == team) | (df.team_away == team)) & (df.schedule_season == next_season) & (df.schedule_week == week)]
                if(next_season_game.shape[0] == 1):
                    next_season_game = next_season_game.iloc[0]
                    if(next_season_game.team_home == team):
                        df.loc[(df.team_home == team) & (df.schedule_season == next_season) & (df.schedule_week == week), 'team_home_lastseason_win_pct'] = 0 if games_played == 0 else wins/games_played
                    elif(next_season_game.team_away == team):
                        df.loc[(df.team_away == team) & (df.schedule_season == next_season) & (df.schedule_week == week), 'team_away_lastseason_win_pct'] = 0 if games_played == 0 else wins/games_played


    # create new result column 
    df['result'] = (df.score_home > df.score_away).astype(int)

    df.loc[df.team_favorite_id == df.team_home, 'home_favorite'] = 1
    df.loc[df.team_favorite_id == df.team_away, 'away_favorite'] = 1
    df.home_favorite.fillna(0, inplace=True)
    df.away_favorite.fillna(0, inplace=True)

    home_win = "{:.2f}".format((sum((df.result == 1) & (df.stadium_neutral == 0)) / sum(df.stadium_neutral == 0)) * 100)
    away_win = "{:.2f}".format((sum((df.result == 0) & (df.stadium_neutral == 0)) / sum(df.stadium_neutral == 0)) * 100)

    under_line = "{:.2f}".format((sum((df.score_home + df.score_away) < df.over_under_line) / len(df)) * 100)
    over_line = "{:.2f}".format((sum((df.score_home + df.score_away) > df.over_under_line) / len(df)) * 100)
    equal_line = "{:.2f}".format((sum((df.score_home + df.score_away) == df.over_under_line) / len(df)) * 100)

    favored = "{:.2f}".format((sum(((df.home_favorite == 1) & (df.result == 1)) | ((df.away_favorite == 1) & (df.result == 0)))
                            / len(df)) * 100)


    cover = "{:.2f}".format((sum(((df.home_favorite == 1) & ((df.score_away - df.score_home) < df.spread_favorite)) | 
                                ((df.away_favorite == 1) & ((df.score_home - df.score_away) < df.spread_favorite))) # use score_home - score_away because the fav are swap
                            / len(df)) * 100)

    ats = "{:.2f}".format((sum(((df.home_favorite == 1) & ((df.score_away - df.score_home) > df.spread_favorite)) | 
                            ((df.away_favorite == 1) & ((df.score_home - df.score_away) > df.spread_favorite))) 
                        / len(df)) * 100)

    # training and testing 
    train = df.copy()
    test = df.copy()

    X_train = train[['schedule_week', 'spread_favorite', 'over_under_line', 'home_favorite']]
    y_train = train['result']

    X_test = test[['schedule_week', 'spread_favorite', 'over_under_line', 'home_favorite']]
    y_test = test['result']

    clf = MLPClassifier(random_state=1, max_iter=300).fit(X_train, y_train)
    return clf

if __name__ == "__main__":
    main()
