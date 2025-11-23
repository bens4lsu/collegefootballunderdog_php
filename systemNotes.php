<div class="notes-header">November 23, 2025</div>
<div class="notes-body">Fixed a bug with the display of a person's list of all picks for the season.
</div>

<div class="notes-header">August 29, 2024</div>
<div class="notes-body">Fixed problem where system couldn't parse lines when the date on draft kings said "Today" or "Tomorrow" instead of an actual date.</div>

<div class="notes-header">August 8, 2024</div>
<div class="notes-body">Updates for 2024 season.  Lines coming from <a href="https://sportsbook.draftkings.com/leagues/football/ncaaf?wpkw=https%3A%2F%2Fsportsbook.draftkings.com%2Fleagues%2Ffootball%2Fncaaf&wpcn=leagues&wpscn=football%2Fncaaf.com%2Fleagues%2Ffootball%2Fncaaf&wpcn=leagues&wpscn=football%2Fncaaf" target="_blank">draftkings.com</a> for now.
</div>

<div class="notes-header">August 11, 2023</div>
<div class="notes-body">Update the spread lookup to get the current format from vegasinsder.com.  Fix a bug where it was showing this week's picks as options on previous years' pools.
</div>

<div class="notes-header">November 9,2022</div>
<div class="notes-body"><ol><li>Changed the bonus pick logic so that the game that was your first pick doesn't show up in the list again.</li><li>Moved the email handling to a background process so that posting a message doesn't take all week.</li></ol>
</div>

<div class="notes-header">August 22,2022</div>
<div class="notes-body">Rejiggered the code that pulls lines into the system.  They're back to coming from the consensus column at vegasinsider.com.  I already see that my time zone conversion is an hour off.
</div>

<div class="notes-header">December 30,2021</div>
<div class="notes-body">Fixed bug where the system would let you enter more bonus picks than the allowed number for the pool.
</div>

<div class="notes-header">December 18,2021</div>
<div class="notes-body">Changed the system back so that it uses VegasInsider.com for lines, as ESPN wasn't showing lines for very many of the bowl games.
</div>

<div class="notes-header">August 19, 2021</div>
<div class="notes-body">Mailer library upgrade.</div>

<div class="notes-header">December 3, 2020</div>
<div class="notes-body">Some bug fixes:
    <ol><li>Still trying to get the fix where no error shows up when there isn't a spread.  Got it this time?</li>
        <li>Picker page would let you pick >1 bonus pick in a week, which would have wreaked havoc in the scoring and tracking.</li>
        <li>The watchlist radio buttons didn't show up when you were on your bonus pick selection.</li>
        <li>Cliff came up with a very clever way to cheat and kindly pointed it out rather than just using it to dominate the season.  I'll describe if someone asks and the fix is well-tested.  Believe the loophole is closed now though.</li>
        <li>If a bonus pick was used, the text on the first tab still said "Regular Pick" when it showed your picks on screen 1.</li>
    </ol>
</div>

<div class="notes-header">November 17, 2020</div>
<div class="notes-body">Another fix having to do with scraping the lines from ESPN.  Prior to fix, if there was no posted over/under and the home team was favored, the point spread was being listed for the wrong team.</div>

<div class="notes-header">November 10, 2020</div>
<div class="notes-body">Another fix having to do with scraping the lines from ESPN.  Prior to fix, games didn't show up when there wasn't a posted over/under number.</div>

<div class="notes-header">November 1, 2020</div>
<div class="notes-body">Put in a little fix that should handle the issue we saw when there was a Pick-em game.  In the past, the system just hid the games where there was no spread, but in order to avoid questions about whether the game is just missing from the list, it will now show but with a spread of zero.  You're free to pick it, and if the team that shows as an underdog wins, it will show it as a win, and in the system, the zero will be added to your score, so have at if you like.</div>

<div class="notes-header">October 20, 2020</div>
<div class="notes-body">Fixed bug where a litte inconsequential error showed on the screen if your watchlist was empty.</div>

<div class="notes-header">October 15, 2020</div>
<div class="notes-body">New feature alert:  Watchlist selections now persist if you refresh a page, log out and come back later, or even if you switch devices.</div>

<div class="notes-header">October 13, 2020</div>
<div class="notes-body">System now uses ESPN page for lines rather than Vegas Insider.  Compare spreads to <a href="https://www.espn.com/college-football/lines">https://www.espn.com/college-football/lines</a>.</div>

<div class="notes-header">September 4, 2020</div>
<div class="notes-body">Fix for "Show all picks for User X" bug.</div>


<div class="notes-header">September 4, 2020</div>
<div class="notes-body">Don't show games for the following week if the lines are already out the week before.  Allowing picks a week ahead would be a fiasco.</div>


<div class="notes-header">November 5, 2019</div>
<div class="notes-body">Fixed issue with emails going out when notes are added to a pool.  Note that you might have to check your spam folder and train your emails that these messages are ones that you want.  Adding gottakeepitautomated@collegefootballunderdog.com to your contacts might help.</div>


<div class="notes-header">October 12, 2019</div>
<div class="notes-body">I noticed that the All Picks list is longer than the frame on my iphone, and for some reason, it won't scroll so that I can see them all.  So for the short term, I have a link at the top that will open the all picks in its own tab/window so that you can see them all on mobile.</div>

<div class="notes-header">October 4, 2019</div>
<div class="notes-body"><ol><li>Bug fix.  When someone picked an away team underdog, the favorite was actually showing up as their pick.  I introduced that bug just this week, and I've fixed the picks that were messed up that were submitted earlier.</li><li>Finished work to make the poo games disappear.  Now, games will show up if at least one team is an FBS team (and if there is a non-Pick-Em line).</li><li>Minor formatting release notes screen (this page).</li></ol></div>

<div class="notes-header">October 3, 2019</div>
<div class="notes-body"><ol><li>Moved system to new server hosted by Amazon.</li><li>Added system notes tab so as not to clog up the messages (which were meant to give Shelly a platform for insulting others) with this sort of nonsense.</li><li>As I feared, the poll favorite is the option that requires I change code rather than just add or remove teams from a table.  I started work on this, by manually flagging which teams are FBS.  Since it was manual, there's probably a mistake or two.  If anyone wants to double check my work, you can download the table <a href="/downloads/DogOfTheWeekTeamList.csv" target="_blank">here</a>.  (File -> Save, then open it with your spreadsheet program.)  Much appreciated.</li></ol></div>

<div class="notes-header">Poll about how system should work</div>
<div class="notes-body">This poll ran for a couple of weeks in late September, 2019.</div>
<div class="notes-body"><img src="/images/whichGamesPoll.png"></div>
