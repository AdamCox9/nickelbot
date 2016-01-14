The idea of this platform is to expose all of the functionality from each of the crypto trading exchanges. 
This allows for developers to quickly write a bot that works on all of the exchanges instead of learning each API and writing a bot for each exchange.
Each adapter should return the same output as any other adapter for the other exchanges. 
There should be unit tests that test each adapter and test the native API for each exchange.
It is important to maintain the full functionality offered by each exchange and make sure exchanges that don't support some functionality to respond appropriately.
For example, only some exchanges offer margin trading. 
Adapters that don't support margin trading functionality shall respond with appropriate error messages.
It should not be hard to port an existing bot designed for a specific exchange to this platform and have it work across all the exchanges.
There should be plenty of example bots to get the beginner started in no time. 
The advanced user should have all functionality available from any API.
It will still be easy for a developer to build bots that are specifically targeted for an exchange since each exchange might require different strategies.
There are some universal rules that will apply across the exchanges to make a profit. 
This platform allows the developer to easily build a wide range of trading bots.
This platform allows a developer to easily get statistics and summaries from each exchange in the same format.
This allows the developer to perform analysis and make potential profit.
If someone needs to liquidate a digital asset, they will be able to find the most profitable way to do it. 
They can easily create a bot to sell it at an expected percentage above the current price.
They can distribute the sale across all the exchanges to maximize the potential liquidity available.# nickelbot
