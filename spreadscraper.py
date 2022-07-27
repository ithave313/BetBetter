from bs4 import BeautifulSoup
import requests
import pandas as pd

url = "https://sportsbook.draftkings.com/leagues/football/88670561"
proxy = {
    "https": 'https://158.177.252.170:3128',
    "http": 'https://158.177.252.170:3128' 
}
page = requests.get(url)
soup = BeautifulSoup(page.content, "html.parser")

team_names = [t.get_text() for t in soup.find_all('div', class_='event-cell__name-text')]
lines = [t.get_text() for t in soup.find_all('span', class_='sportsbook-odds american default-color')]

over_under = []
spreads = [s.get_text() for s in soup.find_all('span', class_='sportsbook-outcome-cell__line')]
for spread in spreads:
  if "+" not in spread:
    if "-" not in spread:
      over_under.append(spread)
      spreads.remove(spread)

j = 0
k = 1

new_lines = []

for i in range(len(lines)):
  if i == j:
    new_lines.append(lines[i])
    j+=4
  elif i == k:
    new_lines.append(lines[i])
    k+=4
    

data_tuples = list(zip(team_names,spreads))
df = pd.DataFrame(data_tuples, columns=['Team_Name', 'Spread'])

df['ML Odds'] = pd.Series(new_lines)
df['Over/Under'] = pd.Series(over_under)

new_df = df.iloc[26:54]
new_df.reset_index(inplace=True)
new_df = new_df.drop(columns=['index'])
new_df.to_csv('incoming_spreads.csv')

matchup = []
new_df = pd.DataFrame(columns=['Home Team','Away Team', 'Spread', 'Over/Under', 'Home_Favored'])
home_teams = []
away_teams = []
home_favorites = []
over_unders = []
spreads = []
for index, row in df.iterrows():
  if index % 2 == 0:
    away_teams.append(row['Team_Name']) 
    if '-' in str(row['Spread']):
      home_favorites.append(0)
      spreads.append(row['Spread'])
    over_unders.append(row['Over/Under'])
  else:
    home_teams.append(row['Team_Name'])
    if '-' in str(row['Spread']):
      home_favorites.append(1)
      spreads.append(row['Spread'])
    
new_df['Home Team'] = pd.Series(home_teams)
new_df['Away Team'] = pd.Series(away_teams)
new_df['Spread'] = pd.Series(spreads)
new_df['Over/Under'] = pd.Series(over_unders)
new_df['Home_Favored'] = pd.Series(home_favorites)
print(new_df[0:15])
new_df.to_csv('incoming_matchups.csv')
