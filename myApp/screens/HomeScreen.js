import React, { useEffect, useState, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  Button,
  Alert
} from 'react-native';
import axios from 'axios';
import { useFocusEffect } from '@react-navigation/native';
import { FontAwesome } from '@expo/vector-icons';
import TriviaCard from '../components/TriviaCard';

export default function HomeScreen({ navigation, route }) {
  const { loggedInUser } = route.params || {};
  const [triviaList, setTriviaList] = useState([]);
  const [error, setError] = useState('');

  useEffect(() => {
    if (!loggedInUser) {
      navigation.navigate('Login');
    }
  }, [loggedInUser]);

  useFocusEffect(
    useCallback(() => {
      fetchTrivia();
    }, [])
  );

  const fetchTrivia = () => {
    axios.get(`http://10.0.2.2/big4sports/backend/api_trivia.php?username=${loggedInUser}`)
      .then(response => {
        setTriviaList(response.data);
      })
      .catch(err => {
        console.error(err);
        setError(err.message);
      });
  };

  const handleDelete = (id) => {
    Alert.alert(
      'Delete Trivia',
      'Are you sure you want to delete this item?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Delete',
          style: 'destructive',
          onPress: () => {
            axios.delete(`http://10.0.2.2/big4sports/backend/api_trivia.php?id=${id}`)
              .then(res => {
                setTriviaList(prev => prev.filter(item => item.id != id));
              })
              .catch(err => console.error(err));
          }
        }
      ]
    );
  };

  const renderItem = ({ item }) => (
    <TriviaCard
      trivia={item}
      loggedInUser={loggedInUser}
      navigation={navigation}
      onDelete={handleDelete}
    />
  );

  return (
    <View style={styles.container}>
      {/* New Fancy Title with Trophy */}
      <View style={styles.header}>
        <Text style={styles.headerText}>Big4Sports</Text>
        <FontAwesome name="trophy" size={28} color="#e6b800" style={styles.trophy} />
      </View>

      {error ? (
        <Text style={{ color: 'red', marginBottom: 10 }}>{error}</Text>
      ) : null}

      {/* Create Button */}
      <Button
        title="Create New Trivia"
        onPress={() => navigation.navigate('CreateTrivia', { loggedInUser })}
      />

      {/* Reset Guesses Button */}
      <Button
        title="Reset My Guesses"
        color="red"
        onPress={() => {
          Alert.alert(
            "Reset All Guesses",
            "Are you sure you want to reset all your guesses?",
            [
              { text: "Cancel", style: "cancel" },
              {
                text: "Reset",
                style: "destructive",
                onPress: () => {
                  axios.post('http://10.0.2.2/big4sports/backend/api_trivia.php', {
                    action: 'reset_guesses',
                    username: loggedInUser
                  })
                  .then((res) => {
                    alert(res.data.message);
                    fetchTrivia();  // Refresh list
                  })
                  .catch((err) => {
                    alert("Error resetting guesses");
                    console.error(err);
                  });
                }
              }
            ]
          );
        }}
      />

      {/* List of Trivia */}
      <FlatList
        data={triviaList}
        keyExtractor={(item) => item.id.toString()}
        renderItem={renderItem}
        contentContainerStyle={{ paddingVertical: 10 }}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    paddingHorizontal: 16,
    flex: 1,
    backgroundColor: '#f2f2f2',
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 15,
    marginBottom: 10,
  },
  headerText: {
    fontSize: 28,
    fontWeight: 'bold',
    marginRight: 10,
    color: '#1c1c1c',
  },
  trophy: {
    marginTop: 2,
  },
});
