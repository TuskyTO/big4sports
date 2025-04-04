import React from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import HomeScreen from './screens/HomeScreen';
import ReadScreen from './screens/ReadScreen';
import CreateScreen from './screens/CreateScreen';
import UpdateScreen from './screens/UpdateScreen';
import DeleteScreen from './screens/DeleteScreen';
import LoginScreen from './screens/LoginScreen';
import RegisterScreen from './screens/RegisterScreen';

const Stack = createStackNavigator();

export default function App() {
  return (
    <NavigationContainer>
      <Stack.Navigator>
        <Stack.Screen name="Login" component={LoginScreen} />
        <Stack.Screen name="Register" component={RegisterScreen} />
        <Stack.Screen name="Home" component={HomeScreen} options={{ title: 'Trivia List' }} />
        <Stack.Screen name="ReadTrivia" component={ReadScreen} options={{ title: 'Read Trivia' }} />
        <Stack.Screen name="CreateTrivia" component={CreateScreen} options={{ title: 'Create Trivia' }} />
        <Stack.Screen name="UpdateTrivia" component={UpdateScreen} options={{ title: 'Update Trivia' }} />
        <Stack.Screen name="DeleteTrivia" component={DeleteScreen} options={{ title: 'Delete Trivia' }} />
      </Stack.Navigator>
    </NavigationContainer>
  );
}
